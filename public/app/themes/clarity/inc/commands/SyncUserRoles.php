<?php

/**
 * This command is used to sync user roles from codebase to database.
 * This update operation should be run to sync/migrate user roles.
 * 
 * Usage:
 *  dry-run: wp sync-user-roles
 *  real-run: wp sync-user-roles sync
 */

class SyncUserRoles
{
    public function __invoke($args): void
    {
        error_reporting(0);

        WP_CLI::log('SyncUserRoles starting');

        if (empty($args[0]) || $args[0] !== 'sync') {
            WP_CLI::log('SyncUserRoles dry run complete');
            return;
        }

        $theme_dir = get_stylesheet_directory();

        require_once $theme_dir . '/inc/admin/users/add-agency-admin.php';
        require_once $theme_dir . '/inc/admin/users/add-agency-editor.php';
        require_once $theme_dir . '/inc/admin/users/add-regional-editor.php';
        require_once $theme_dir . '/inc/admin/users/add-team-author.php';
        require_once $theme_dir . '/inc/admin/users/add-team-lead.php';
        require_once $theme_dir . '/inc/admin/users/delete-roles.php';

        WP_CLI::log('SyncUserRoles complete');
    }
}

// 1. Register the instance for the callable parameter.
$instance = new SyncUserRoles();
WP_CLI::add_command('sync-user-roles', $instance);

// 2. Register object as a function for the callable parameter.
WP_CLI::add_command('sync-user-roles', 'SyncUserRoles');
