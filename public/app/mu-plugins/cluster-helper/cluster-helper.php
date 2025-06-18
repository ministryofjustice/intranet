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
     * Set the nginx hosts option with a new array of hosts.
     * 
     * @param array $hosts An associative array of nginx hosts with their timestamps.
     *                     Example: 
     *                     [
     *                         'http://host1:8080' => ['updated_at' => 1234567890, 'unresolved_count' => 0],
     *                         'http://host2:8080' => ['updated_at' => 1234567890, 'unresolved_count' => 2]
     *                     ]
     * @return void
     */
    public function setNginxHosts(array $hosts): void
    {
        // Update the option with the new array of nginx hosts
        update_option($this::OPTION_KEY, maybe_serialize($hosts), false);
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
        $current_nginx_hosts = $this->getNginxHosts();

        $updated = isset($current_nginx_hosts[$host]);

        $current_nginx_hosts[$host] = [
            'updated_at' => time(),
            'unresolved_count' => 0,
        ];

        // Update the option with the modified array
        $this->setNginxHosts($current_nginx_hosts);

        return $updated;
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
        $current_nginx_hosts = $this->getNginxHosts();

        $found = array_key_exists($host, $current_nginx_hosts);

        // Check if the hostname exists
        if ($found) {
            unset($current_nginx_hosts[$host]);
            // Update the option with the modified array
            $this->setNginxHosts($current_nginx_hosts);
        }

        return $found;
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
        $nginx_hosts_have_been_updated = false;
        $now = time();
        $threshold = 0; //24 * 60 * 60; // 24 hours in seconds


        foreach ($nginx_hosts as $host => $values) {

            // Ensure the timestamps are set, if not, remove the entry.
            if (!isset($values['updated_at']) || !isset($values['unresolved_count'])) {
                unset($nginx_hosts[$host]);
                $nginx_hosts_have_been_updated = true;
                continue;
            }

            // Check if the host has been updated in the last 24 hours, and return early if it is.
            if (($now - $values['updated_at']) < $threshold) {
                continue;
            }

            $hostname = parse_url($host, PHP_URL_HOST);
            $resolved_ip = gethostbyname($hostname);

            if ($hostname === $resolved_ip) {
                // If the hostname does not resolve, it will return the hostname itself.
                // So we can safely assume it does not resolve.
                // We increment the unresolved count.
                $nginx_hosts[$host]['unresolved_count']++;
                $nginx_hosts_have_been_updated = true;
            }

            // If the unresolved count is greater than 3, we remove the host.
            // This is to prevent the array from growing indefinitely with unresolved hosts.
            if ($nginx_hosts[$host]['unresolved_count'] > 3) {
                unset($nginx_hosts[$host]);
            }
        }

        if ($nginx_hosts_have_been_updated) {
            // Update the option with the updated/cleaned up array.
            $this->setNginxHosts($nginx_hosts);
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
