<?php
/*
 * Plugin Name: Disable/Configure Plugins
 * Description: Lets you disable/configure plugins based on environment variables
 * Author:      Chris Sewell
 * @see https://wordpress.stackexchange.com/a/281114
 *
 * This is a "Must-Use" plugin. Code here is loaded automatically before regular plugins load.
 * This is the only place from which regular plugins can be disabled programmatically.
 */

/* Disable specified plugins in non-development environments */
if (getenv('WP_ENV') !== 'development' && is_admin()) {
    $plugins = [
        'wordpress-importer/wordpress-importer.php'
    ];
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins($plugins);
}
