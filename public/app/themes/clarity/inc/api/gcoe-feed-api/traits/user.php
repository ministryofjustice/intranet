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
        // If the user is an administrator, they have permission.
        if(current_user_can('administrator')) {
            return true;
        }

        // Use the global $moj_auth as it has the jwtHasRole utility function.
        global $moj_auth;

        return $moj_auth?->jwtHasRole('gcoe');
    }
}
