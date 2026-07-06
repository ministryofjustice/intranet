<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

trait User
{
    /**
     * Does the current user have permission to: 
     * - access the Synergy feed, and
     * - create an application password
     * 
     * @return bool True if the user has permissions, false otherwise.
     */
    public static function userHasPermission(): bool
    {
        // If the user is an administrator, they have permission.
        if (current_user_can('administrator')) {
            return true;
        }

        // Use the global $moj_auth as it has the jwtHasRole utility function.
        global $moj_auth;

        return $moj_auth?->jwtHasRole('synergy') && current_user_can('synergy');
    }


    /**
     * This function allows the user to access the /wp/v2/users/me REST route
     * if they are an administrator. If not, it returns the result of the
     * rest_authentication_errors filter.
     * 
     * This is a thorough workaround for the security plugin that blocks access to the
     * /wp/v2/users/<own_user_id>/application-passwords REST route for all users.
     *
     * This allows admins to create application passwords for synergy users via the WP admin UI.
     *
     * @param WP_Error|null The result of the rest_authentication_errors filter, so far.
     * @return WP_Error|null The filtered result, null if conditions are met.
     */
    public static function allowUserRestRouteForAdmins($result)
    {
        // Check if class exists, if not then do noting.
        if (!class_exists('MOJComponents\Security\FilterRestAPI')) {
            return $result;
        }

        // Check if the passed in value is an error, if not then do nothing.
        if (!is_wp_error($result)) {
            return $result;
        }

        // Check if we are an administrator, if not then do nothing.
        if (!current_user_can('administrator')) {
            return $result;
        }

        // Check if we are on the specific REST API route, if not then do nothing.
        $rest_route = $GLOBALS['wp']->query_vars['rest_route'];

        if (!$rest_route) {
            return $result;
        }

        // Is rest route in the pattern we are looking for? Where user_id is for the user being edited.
        // e.g. /wp/v2/users/<user_id>/application-passwords(/<application_password_id>)?
        $user_id = preg_replace('/^\/wp\/v2\/users\/(\d+)\/application-passwords(\/[0-9a-fA-F\-]{36})?$/', '$1', $rest_route);

        if (!$user_id || !is_numeric($user_id)) {
            // If the user ID is not numeric, then return the result.
            return $result;
        }

        // The user must be role 'synergy'.
        $user_role = get_userdata($user_id)->roles[0] ?? '';

        if ('synergy' !== $user_role) {
            // If the user is not a synergy user, then return the result.
            return $result;
        }

        // Check if the referrer is the allowed referrer, if not then do nothing.
        $allowed_referrer = get_admin_url(null, 'user-edit.php?user_id=' . $user_id);

        if (!str_starts_with(wp_get_referer(), $allowed_referrer)) {
            return $result;
        }

        // Check if the error message is the one we are looking for, if not then do nothing.
        $error_messages = $result->get_error_messages();
        if (count($error_messages) !== 1 || $error_messages[0] !== esc_html__('Only authenticated users can access the REST API.')) {
            return $result;
        }

        // Check if the error data is the one we are looking for, if not then do nothing.
        $error_data = $result->get_error_data();
        if (count($error_data) !== 1 || $error_data['status'] !== 403) {
            return $result;
        }

        // If we are here, the whe have satisfied the following conditions:
        // 1. The class exists.
        // 2. The passed in value is an error(s).
        // 3. The user is an administrator.
        // 4. The REST API route is one of the allowed routes.
        // 5. The referrer is the allowed referrer.
        // 6. The error message is the one we are looking for.
        // 7. The error data is the one we are looking for.
        // So we can return null to allow the admin user to access the REST API route.

        return null;
    }
}
