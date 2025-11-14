<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

trait User
{
    /**
     * Does the current user have permission to: 
     * - access the GCoE feed
     * 
     * @return bool True if the user has permissions, false otherwise.
     */
    public static function userHasPermission(): bool
    {
        // TODO - fix
        return true;

        // If the user is an administrator, they have permission.
        return current_user_can('administrator');
    }
}
