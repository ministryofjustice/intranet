<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

use WP_Roles;

/**
 * This file is only for removing default/custom user groups
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 */


class RemoveUnusedRoles
{
    public function __construct()
    {
        add_action('init', [$this, 'removeRoles']);
    }

    public function removeRoles()
    {
        $wp_roles = new WP_Roles();

        $wp_roles->remove_role('agency_admin_editor');
        $wp_roles->remove_role('author');
        $wp_roles->remove_role('contributor');
        $wp_roles->remove_role('editor');
        $wp_roles->remove_role('section_editor');
    }
}
