<?php

namespace MOJ;

// Do not allow access outside WP
defined('ABSPATH') || exit;

use Exception;
use WP_REST_Response;
use Roots\WPConfig\Config;

/**
 * This class contains methods related to managing Nginx hosts in a WordPress cluster.
 */

class ClusterHelper
{
    const OPTION_KEY = 'cluster_helper_nginx_hosts';

    /**
     * Constructor
     * Initializes the class and sets up the necessary hooks.
     */
    public function __construct()
    {
        // Create a rest API endpoint, for use in the cleanupOldNginxHosts function.
        // It's reachable at /wp-json/cluster-helper/v1/nginx-hosts
        add_action('rest_api_init', [$this, 'registerCheckHomeUrlRoute']);

        // If WP_CLI is defined, we do not run the scheduled tasks or dashboard widget.
        if (defined('WP_CLI') && WP_CLI) {
            return;
        }

        // Create a 1 minute schedule
        add_filter('cron_schedules', [$this, 'addOneMinuteCronSchedule']);

        // Set up a scheduled task to ensure Nginx hosts are registered and cleaned up.
        if (!wp_next_scheduled('cluster_helper_schedule')) {
            wp_schedule_event(time(), 'one_minute', 'cluster_helper_schedule');
        }

        // Ensure current host in the the list of Nginx hosts.
        add_action('cluster_helper_schedule', [$this, 'registerSelf'], 10);

        // Add the cleanup function to the scheduled task.
        add_action('cluster_helper_schedule', [$this, 'cleanupOldNginxHosts'], 20);

        // Add a Dashboard widget to display a table of Nginx hosts.
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);
    }

    /**
     * Get the current nginx hosts from the option.
     *
     * @param string $format The format to return the nginx hosts, either full or hostnames.
     * @return array An array of nginx hosts.
     *               - If $format is 'full', it returns the full array of nginx hosts with their timestamps.
     *               - If $format is 'hosts', it returns an array of hostnames only.
     *               - If $format is invalid, it defaults to 'full'.
     * If the option does not exist or is not an array, it returns an empty array.
     */
    public static function getNginxHosts($format = 'full'): array
    {
        // Validate the format parameter
        if (!in_array($format, ['full', 'hosts'])) {
            $format = 'full';
        }

        // Clear the cache for the nginx hosts option - always read from the database.
        wp_cache_delete('cluster_helper_nginx_hosts', 'options');

        // Get the current nginx hosts from the option
        $nginx_hosts_string = get_option(self::OPTION_KEY, '');
        $nginx_hosts = maybe_unserialize($nginx_hosts_string);

        // Ensure it's an array
        $nginx_hosts = is_array($nginx_hosts) ? $nginx_hosts : [];

        // If the format is 'full', return the full array
        if ($format === 'full') {
            return $nginx_hosts;
        }

        // Return the array of hostnames only
        return array_keys($nginx_hosts);
    }

    /**
     * Upsert an nginx host in the option.
     * Set the entry with updated_at timestamp and unresolved_count.
     *
     * @param string     $host The host to upsert.
     * @param int        $unresolved_count The unresolved count to set for the host (default is 0).
     * @return bool|null Returns true if the host was already present and updated, false if it was newly added,
     *                   or null if there was an error during the operation.
     */
    public function upsertNginxHost(string $host, int $unresolved_count = 0): bool|null
    {
        // Start a transaction to ensure atomicity
        global $wpdb;
        $attempts = 0;
        $max_attempts = 5;
        $return_value = null;

        while ($attempts < $max_attempts && $return_value === null) {
            $wpdb->query('START TRANSACTION');

            try {
                $nginx_hosts = $this->getNginxHosts();

                $already_exists = isset($nginx_hosts[$host]);

                $nginx_hosts[$host] = [
                    'updated_at' => time(),
                    'unresolved_count' => $unresolved_count,
                ];

                // Update the option with the modified array
                update_option(self::OPTION_KEY, serialize($nginx_hosts), false);

                // Commit the transaction
                $wpdb->query('COMMIT');

                $return_value = $already_exists;
            } catch (Exception $e) {
                // Rollback the transaction in case of an error
                $wpdb->query('ROLLBACK');
                error_log(sprintf('Error upserting nginx host %s: %s', $host, $e->getMessage()));
                $attempts++;
            }
        }

        return $return_value;
    }

    /**
     * Delete an nginx host from the option.
     * If the host exists, it will be removed from the array.
     * If it does not exist, a message will be logged indicating nothing to remove.
     *
     * @param string $host The host to delete.
     * @return bool|null Returns true if the host was found and deleted, false if it was not found,
     *                  or null if there was an error during the operation.
     */
    public function deleteNginxHost(string $host): bool|null
    {
        // Start a transaction to ensure atomicity
        global $wpdb;
        $attempts = 0;
        $max_attempts = 5;
        $return_value = null;

        while ($attempts < $max_attempts && $return_value === null) {
            $wpdb->query('START TRANSACTION');

            try {
                $nginx_hosts = $this->getNginxHosts();

                $found = array_key_exists($host, $nginx_hosts);

                // Check if the hostname exists
                if ($found) {
                    unset($nginx_hosts[$host]);
                    // Update the option with the modified array
                    update_option(self::OPTION_KEY, serialize($nginx_hosts), false);
                }

                // Commit the transaction
                $wpdb->query('COMMIT');

                $return_value = $found;
            } catch (Exception $e) {
                // Rollback the transaction in case of an error
                $wpdb->query('ROLLBACK');
                error_log(sprintf('Error deleting nginx host %s: %s', $host, $e->getMessage()));
                $attempts++;
            }
        }

        return $return_value;
    }

    /**
     * Register a REST route to check if the current host is an Nginx host *for this application*.
     *
     * In the cleanup script, we need to know if a URL is still associated with this application.
     * This endpoint accepts a `home_url` parameter and checks if it matches the current site's home URL.
     *
     * e.g. http://172.0.0.12/wp-json/cluster-helper/v1/check-home-url?home-url=https://dev.intranet.justice.gov.uk
     *      will return true if the home URL matches the current site's home URL,
     *      or false if it does not match.
     *
     * @return void
     */
    public static function registerCheckHomeUrlRoute(): void
    {
        register_rest_route('cluster-helper/v1', '/check-home-url', [
            'permission_callback' => '__return_true', // Allow public access.
            'args' => [
                'home-url' => [
                    'required' => true,
                    'validate_callback' => fn($param) => filter_var($param, FILTER_VALIDATE_URL) !== false,
                ],
            ],
            'callback' => fn($request) => new WP_REST_Response($request->get_param('home_url') === get_home_url())
        ]);
    }

    public function registerSelf(): void
    {
        // Register the current host with the cluster helper.
        $this->upsertNginxHost(Config::get('NGINX_HOST'));
    }

    /**
     * Clean up old Nginx hosts that do not resolve and are older than 48 hours.
     * This function is scheduled to run hourly.
     *
     * @return array
     */
    public function cleanupOldNginxHosts(): array
    {
        $nginx_hosts = $this->getNginxHosts('full');
        $now = time();
        $threshold = 24 * 60 * 60; // 24 hours in seconds

        foreach ($nginx_hosts as $host => $values) {
            // Ensure the timestamps are set, if not, remove the entry.
            if (!isset($values['updated_at']) || !isset($values['unresolved_count'])) {
                $this->deleteNginxHost($host);
                continue;
            }

            // Check if the host has been updated in the last 24 hours, and return early if it is.
            if (($now - $values['updated_at']) < $threshold) {
                continue;
            }

            // Make a request to the check-home-url endpoint of the host.
            $response = wp_remote_get($host . '/wp-json/cluster-helper/v1/check-home-url?home-url=' . urlencode(get_home_url()));

            // Check for a couple of things:
            // 1. If the request was successful and there was no error.
            // 2. If the response body is 'true', indicating that the host is still running this application.
            if (!is_wp_error($response) && wp_remote_retrieve_body($response) === 'true') {
                // Both conditions are met, we can assume the host is still valid and reset the unresolved count to zero.
                if ($values['unresolved_count'] > 0) {
                    $this->upsertNginxHost($host, 0);
                }
                continue;
            }

            // Here, the request failed, so we need to handle the unresolved count.

            // If the current unresolved count is greater than 3, we remove the host.
            if ($values['unresolved_count'] >= 3) {
                $this->deleteNginxHost($host);
                continue;
            }

            // Otherwise, we update the host with an incremented unresolved count.
            $this->upsertNginxHost($host, $values['unresolved_count'] + 1);
        }

        return $this->getNginxHosts('full');
    }

    /**
     * Add a dashboard widget to display the Nginx hosts.
     *
     * This widget will only be added if the current user has administrator capabilities.
     *
     * @return void
     */
    public function addDashboardWidget(): void
    {
        if (!current_user_can('administrator')) {
            return;
        }

        wp_add_dashboard_widget(
            'cluster_helper_nginx_hosts',
            'Nginx Hosts',
            [$this, 'renderDashboardWidget']
        );
    }

    /**
     * Render the dashboard widget that displays the Nginx hosts.
     *
     * This method retrieves the Nginx hosts and displays them in a table format.
     * If no hosts are registered, it displays a message indicating that.
     *
     * @return void
     */
    public function renderDashboardWidget(): void
    {
        $nginx_hosts = $this->getNginxHosts('full');

        if (empty($nginx_hosts)) {
            echo '<p>No Nginx hosts registered.</p>';
            return;
        }

        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>Host</th><th>Updated At</th><th>Unresolved Count</th></tr></thead>';
        echo '<tbody>';
        foreach ($nginx_hosts as $host => $values) {
            $updated_at = date('Y-m-d H:i:s', $values['updated_at']);
            $unresolved_count = $values['unresolved_count'];
            echo "<tr>
                                <td>{$host}</td>
                                <td>{$updated_at}</td>
                                <td>{$unresolved_count}</td>
                              </tr>";
        }
        echo '</tbody>';
        echo '</table>';
    }

    /**
     * Adds a custom cron schedule of 1 minute.
     *
     * @param array $schedules
     * @return array
     */
    public function addOneMinuteCronSchedule(array $schedules): array
    {
        if(!isset($schedules['one_minute'])) {
            $schedules['one_minute'] = [
                'interval' => 60,
                'display' => 'Every Minute'
            ];
        }

        return $schedules;
    }
}

if (!defined('WP_CLI') || !WP_CLI) {
    // If WP_CLI is not defined, we instantiate the ClusterHelper class.
    new ClusterHelper();
}
