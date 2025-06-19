<?php

// Do not allow access outside WP
defined('ABSPATH') || exit;

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
        // If WP_CLI is defined, we do not run the scheduled tasks or dashboard widget.
        if (defined('WP_CLI') && WP_CLI) {
            return;
        }

        // Set up a scheduled task to clean up old Nginx hosts.
        if (!wp_next_scheduled('cluster_helper_cleanup_nginx_hosts')) {
            wp_schedule_event(time(), 'hourly', 'cluster_helper_cleanup_nginx_hosts');
        }

        // Add the cleanup function to the scheduled task.
        add_action('cluster_helper_cleanup_nginx_hosts', [$this, 'cleanupOldNginxHosts']);

        // Add a Dashboard widget to display a table of Nginx hosts.
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);
    }

    /**
     * Get the current nginx hosts from the option.
     * 
     * 
     * @param string $format The format to return the nginx hosts, either full or hostnames.
     * @return array An array of nginx hosts.
     *               - If $format is 'full', it returns the full array of nginx hosts with their timestamps.
     *               - If $format is 'hosts', it returns an array of hostnames only.
     *               - If $format is invalid, it defaults to 'full'.
     * If the option does not exist or is not an array, it returns an empty array.
     */
    public function getNginxHosts($format = 'full'): array
    {
        // Validate the format parameter
        if (!in_array($format, ['full', 'hosts'])) {
            $format = 'full';
        }

        // Get the current nginx hosts from the option
        $nginx_hosts_string = get_option($this::OPTION_KEY, '');
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
     * @param string $host The host to upsert.
     * @return bool Returns true if the host was already present and updated, false if it was newly added.
     */
    public function upsertNginxHost(string $host): bool
    {
        // Start a transaction to ensure atomicity
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            $nginx_hosts = $this->getNginxHosts();

            $already_exists = isset($nginx_hosts[$host]);

            $nginx_hosts[$host] = [
                'updated_at' => time(),
                'unresolved_count' => 0,
            ];

            // Update the option with the modified array
            update_option($this::OPTION_KEY, serialize($nginx_hosts), false);

            // Commit the transaction
            $wpdb->query('COMMIT');

            return $already_exists;
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $wpdb->query('ROLLBACK');
            error_log(sprintf('Error upserting nginx host %s: %s', $host, $e->getMessage()));
            return false;
        }
    }

    /**
     * Delete an nginx host from the option.
     * If the host exists, it will be removed from the array.
     * If it does not exist, a message will be logged indicating nothing to remove.
     * 
     * @param string $host The host to delete.
     * @return bool Returns true if the host was found and deleted, false if it was not found.
     */
    public function deleteNginxHost(string $host): bool
    {
        // Start a transaction to ensure atomicity
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            $nginx_hosts = $this->getNginxHosts();

            $found = array_key_exists($host, $nginx_hosts);

            // Check if the hostname exists
            if ($found) {
                unset($nginx_hosts[$host]);
                // Update the option with the modified array
                update_option($this::OPTION_KEY, serialize($nginx_hosts), false);
            }

            // Commit the transaction
            $wpdb->query('COMMIT');

            return $found;
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $wpdb->query('ROLLBACK');
            error_log(sprintf('Error deleting nginx host %s: %s', $host, $e->getMessage()));
            return false;
        }
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
        $threshold = 0; //24 * 60 * 60; // 24 hours in seconds

        // An array of hosts to delete e.g. ['host1', 'host2']
        $nginx_hosts_to_delete = [];

        // An array of hosts to update e.g. ['host1' => ['updated_at' => 1234567890, 'unresolved_count' => 0]]
        $nginx_hosts_to_update = [];

        foreach ($nginx_hosts as $host => $values) {
            // Ensure the timestamps are set, if not, remove the entry.
            if (!isset($values['updated_at']) || !isset($values['unresolved_count'])) {
                $nginx_hosts_to_delete[] = $host;
                continue;
            }

            // Check if the host has been updated in the last 24 hours, and return early if it is.
            if (($now - $values['updated_at']) < $threshold) {
                continue;
            }

            $hostname = parse_url($host, PHP_URL_HOST);
            $resolved_ip = gethostbyname($hostname);

            if ($hostname !== $resolved_ip) {
                continue; // Hostname resolves, so we skip it.
            }

            // If the hostname does not resolve, it will return the hostname itself.
            // So we can safely assume it does not resolve.

            // If the current unresolved count is greater than 3, we remove the host.
            if ($values['unresolved_count'] > 3) {
                $nginx_hosts_to_delete[] = $host;
                continue;
            }

            // Otherwise, we update the host with an incremented unresolved count.
            $nginx_hosts_to_update[$host] = [
                'unresolved_count' => $values['unresolved_count'] + 1,
            ];
        }

        if (sizeof($nginx_hosts_to_delete) || sizeof($nginx_hosts_to_update)) {
            global $wpdb;
            // Start a transaction to ensure atomicity
            // Important that we don't do anything slow here, so we can keep the transaction short.
            $wpdb->query('START TRANSACTION');

            try {
                // Re-read the original option to ensure we have the latest data.
                $tx_nginx_hosts = $this->getNginxHosts('full');

                // Update the hosts that need to be updated.
                $tx_nginx_hosts = array_merge($tx_nginx_hosts, $nginx_hosts_to_update);

                // Remove the hosts that need to be deleted.
                foreach ($nginx_hosts_to_delete as $host) {
                    if (isset($tx_nginx_hosts[$host])) {
                        unset($tx_nginx_hosts[$host]);
                    }
                }

                // Update the option with the modified array.
                update_option($this::OPTION_KEY, serialize($tx_nginx_hosts), false);

                // Commit the transaction
                $wpdb->query('COMMIT');

                return $tx_nginx_hosts;
            } catch (Exception $e) {
                // Rollback the transaction in case of an error
                $wpdb->query('ROLLBACK');
                error_log(sprintf('Error cleaning up nginx hosts: %s', $e->getMessage()));
            }
        }

        return $nginx_hosts;
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
}

if (!defined('WP_CLI') || !WP_CLI) {
    // If WP_CLI is not defined, we instantiate the ClusterHelper class.
    new ClusterHelper();
}
