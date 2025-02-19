<?php

/**
 * This command is used to sync user roles from codebase to database.
 * This update operation should be run to sync/migrate user roles.
 * 
 * Usage:
 *  dry-run: wp sync-user-roles
 *  real-run: wp sync-user-roles sync
 */

namespace MOJ\Intranet;

use WP_CLI;

class SyncUserRoles
{
    public function __invoke($args): void
    {
        WP_CLI::log('SyncUserRoles: starting');

        if (empty($args[0]) || $args[0] !== 'sync') {
            WP_CLI::log('SyncUserRoles: dry run complete');
            return;
        }

        $theme_dir = get_stylesheet_directory();

        require $theme_dir . '/inc/admin/users/role-class.php';

        // Creating roles
        require $theme_dir . '/inc/admin/users/add-agency-admin.php';
        require $theme_dir . '/inc/admin/users/add-agency-editor.php';
        require $theme_dir . '/inc/admin/users/add-regional-editor.php';
        require $theme_dir . '/inc/admin/users/add-team-author.php';
        require $theme_dir . '/inc/admin/users/add-team-lead.php';

        // Add capabilities to existing roles
        require $theme_dir . '/inc/admin/users/add-acf-capabilities.php';
        require $theme_dir . '/inc/admin/users/add-notes-from-antonia.php';
        require $theme_dir . '/inc/admin/users/add-subscriber.php';
        require $theme_dir . '/inc/admin/users/add-team-roles.php';

        // Delete unused roles
        require $theme_dir . '/inc/admin/users/delete-roles.php';

        $blog_ids = is_multisite() ? get_sites(['fields' => 'ids']) : [null];

        foreach ($blog_ids as $blog_id) {
            // If we are on a multisite then switch to a blog.
            is_multisite() && switch_to_blog($blog_id);

            (new AgencyAdminRole)->upsertRole();
            (new AgencyEditorRole)->upsertRole();
            (new RegionalEditorRole)->upsertRole();
            (new TeamAuthorRole)->upsertRole();
            (new TeamLeadRole)->upsertRole();

            new AddAcfCapabilities();
            new AddNotesFromAntoniaCapabilities();
            new RemoveSubscriberCapabilities();
            new AddTeamRolesToAdministrator();

            new RemoveUnusedRoles();

            if (is_multisite()) {
                WP_CLI::log('SyncUserRoles: synced on blog id: ' . $blog_id);
            } else {
                WP_CLI::log('SyncUserRoles: synced on single-site');
            }
        }

        WP_CLI::log('SyncUserRoles: complete');
    }
}

// 1. Register the instance for the callable parameter.
$instance = new SyncUserRoles();
WP_CLI::add_command('sync-user-roles', $instance);

// 2. Register object as a function for the callable parameter.
WP_CLI::add_command('sync-user-roles', 'MOJ\Intranet\SyncUserRoles');
