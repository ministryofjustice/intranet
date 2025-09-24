<?php

namespace MOJ;

// Do not allow access outside WP
defined('ABSPATH') || exit;

/**
 * This class is related to WP_CLI commands for registering and deregistering the current Nginx host.
 *
 * Usage:
 * - wp cluster-helper register-self
 * - wp cluster-helper register-host <host>
 * - wp cluster-helper deregister-self
 * - wp cluster-helper deregister-host <host>
 * - wp cluster-helper get-hosts
 * - wp cluster-helper cleanup-hosts
 */

use WP_CLI;
use Roots\WPConfig\Config;

class ClusterHelperCommands
{
    public $nginx_host;

    public $cluster_helper;

    /**
     * Constructor
     * Initializes the class and sets up the necessary hooks.
     */
    public function __construct()
    {
        $this->nginx_host = Config::get('NGINX_HOST');

        $this->cluster_helper = new ClusterHelper();
    }

    /**
     * Invoke method, for when the command is called.
     */
    public function __invoke($args): void
    {
        error_reporting(0);

        switch ($args[0] ?? '') {
            case 'register-self':
                $updated = $this->cluster_helper->upsertNginxHost($this->nginx_host);
                if ($updated) {
                    WP_CLI::log('Nginx host already registered: ' . $this->nginx_host);
                } else {
                    WP_CLI::log('Registered self with Nginx host: ' . $this->nginx_host);
                }
                break;

            case 'register-host':
                $this->cluster_helper->upsertNginxHost($args[1] ?? '');
                WP_CLI::log('Registered host: ' . esc_url($args[1] ?? ''));
                break;

            case 'deregister-self':
                $deleted = $this->cluster_helper->deleteNginxHost($this->nginx_host);
                if ($deleted) {
                    WP_CLI::log('Deregistered self from Nginx host: ' . $this->nginx_host);
                } else {
                    WP_CLI::log('Nginx host not found for deregistration: ' . $this->nginx_host);
                }
                break;

            case 'deregister-host':
                $this->cluster_helper->deleteNginxHost($args[1] ?? '');
                WP_CLI::log('Deregistered host: ' . esc_url($args[1] ?? ''));
                break;

            case 'get-hosts':
                $nginx_hosts = $this->cluster_helper->getNginxHosts();
                WP_CLI::log(print_r($nginx_hosts, true));
                break;

            case 'cleanup-hosts':
                WP_CLI::log('Before: ' . print_r($this->cluster_helper->getNginxHosts(), true));
                $updates = $this->cluster_helper->cleanupOldNginxHosts();
                WP_CLI::log('After: ' . print_r($updates, true));
                break;

            default:
                WP_CLI::log('ClusterHelper command not recognized');
                break;
        }
    }
}



if (defined('WP_CLI') && WP_CLI) {
    $cluster_helper_commands = new ClusterHelperCommands();
    // 1. Register the instance for the callable parameter.
    WP_CLI::add_command('cluster-helper', $cluster_helper_commands);

    // 2. Register object as a function for the callable parameter.
    WP_CLI::add_command('cluster-helper', 'MOJ\ClusterHelperCommands');
}
