<?php

/**
 * Modifications to adapt the wordpress-simple-history plugin.
 *
 * @package Clarity
 **/

namespace MOJ\Intranet;

use Simple_History;


if (!defined('ABSPATH')) {
    exit;
}

/**
 * Actions and filters related to WordPress Simple History plugin.
 */
class SimpleHistory
{

    public function __construct()
    {
        // Check if the Simple History plugin is active, if not, return early.
        if (!defined('SIMPLE_HISTORY_VERSION')) {
            return;
        }

        // Add hooks
        $this->hooks();
    }

    public function hooks()
    {
        // Remove the dropins we don't want
        add_filter('simple_history/core_dropins', [$this, 'removeUnnecessaryDropins'], 10, 1);

        // Remove the sidebar boxes we don't want
        add_filter('simple_history/SidebarDropin/default_sidebar_boxes', [$this, 'removeUnnecessarySideboxes'], 10, 1);

        // Allow certain rest routes to be accessed by admins only.
        add_filter('rest_authentication_errors', [$this, 'allowUserRestRouteForAdmins'], 11);

        // Allow only administrators to view the history page.
        add_filter('simple_history/view_history_capability', fn() => 'administrator');

        // Allow only administrators to view the settings page.
        add_filter('simple_history/view_settings_capability', fn() => 'administrator');

        // Don't show the dashboard widget.
        add_filter('simple_history_show_on_dashboard', '__return_false');

        // Don't show the admin bar menu.
        add_filter('simple_history_show_in_admin_bar', '__return_false');

        // Hide promotional elements
        add_action('admin_head', [$this, 'inlineStyles']);

        // Metadata logging - ignore some custom fields
        add_filter('simple_history/post_logger/meta_keys_to_ignore', [$this, 'filterOutEventsMetaKeys'], 10, 1);

        // Metadata logging - add custom context for ACF fields
        // add_filter('simple_history/post_logger/context', [$this, 'handleAcfContext'], 10, 5);
    }

    /**
     * This function allows the user to access the /wp/v2/users/me REST route
     * if they are an administrator. If not, it returns the result of the
     * rest_authentication_errors filter.
     * 
     * This is a thorough workaround for the security plugin that blocks access to the
     * /wp/v2/users/me REST route for all users.
     * 
     * @param WP_Error|null The result of the rest_authentication_errors filter, so far.
     * @return WP_Error|null The filtered result, null if conditions are met.
     */
    public function allowUserRestRouteForAdmins($result)
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

        $allowed_rest_routes = [
            '/wp/v2/users/me',
            '/simple-history/v1/search-user',
        ];

        if (!$rest_route || !in_array($rest_route, $allowed_rest_routes)) {
            return $result;
        }

        // Check if the referrer is the allowed referrer, if not then do nothing.
        $allowed_referrer = get_admin_url(null, 'admin.php?page=simple_history_admin_menu_page');

        if (wp_get_referer() !== $allowed_referrer) {
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

    /**
     * Remove unnecessary dropins.
     * 
     * @param array - An associative array of the dropins before filtering.
     * @return array - An associative array of the dropins after filtering.
     */
    public function removeUnnecessaryDropins($dropins)
    {
        error_log('Dropins: ' . print_r($dropins, true));

        $remove = [
            // Export is unnecessary as we are not using it.
            'Simple_History\Dropins\Export_Dropin',
            // Filter out the stats dropin so that the vendor bundled js is not loaded.
            'Simple_History\Dropins\Sidebar_Stats_Dropin',
            // Promotional dropin.
            'Simple_History\Dropins\Sidebar_Add_Ons_Dropin',
        ];

        return array_filter($dropins, function ($value) use ($remove) {
            return !in_array($value, $remove);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Remove unnecessary sidebar boxes.
     * 
     * @param array - An associative array of the sidebar boxes before filtering.
     * @return array - An associative array of the sidebar boxes after filtering.
     */
    public function removeUnnecessarySideboxes($sidebar_boxes)
    {
        // For all users remove the review box.
        unset($sidebar_boxes['boxReview']);

        // If not admin, remove the donate and support sidebar boxes
        if (!current_user_can('administrator')) {
            unset($sidebar_boxes['boxDonate']);
            unset($sidebar_boxes['boxSupport']);
        }

        error_log(print_r($sidebar_boxes, true));

        return $sidebar_boxes;
    }

    /**
     * Use inline styles to hide the premium features postbox.
     * 
     * @return void
     */
    public function inlineStyles()
    {
        // Check if we are on our own (simple history) pages
        if ((new Simple_History\Helpers)->is_on_our_own_pages()) {
            // Add inline styles to the admin head
            echo '<style>.sh-PremiumFeaturesPostbox { display: none; }</style>';
        }
    }

    /**
     * Filter out events meta keys.
     * 
     * @param array $arr_meta_keys_to_ignore - An array of meta keys to ignore.
     * @return array - An array of meta keys to ignore after filtering.
     */
    public function filterOutEventsMetaKeys( $arr_meta_keys_to_ignore) {
        return array_merge($arr_meta_keys_to_ignore, array(
            // Ignore our custom event tracking fields.
            // We don't need to track the tracking :)
            '_prior_party_banner_event_timestamp',
            '_prior_party_banner_event_details'
        ));
    }

    /**
     * Handle ACF context.
     * 
     * This function is a work in progress, and needs further development & testing.
     * The aim is to compare the meta data before and after the change, 
     * and log the changes in a diff format that is easy to read.
     * 
     * @param array $context - The context array.
     * @param array $_old_data - The old data array.
     * @param array $_new_data - The new data array.
     * @param array $old_meta - The old meta array.
     * @param array $new_meta - The new meta array.
     * @return array - The context array after handling ACF context.
     */
    public function handleAcfContext($context, $_old_data, $_new_data, $old_meta, $new_meta)
    {
        // Look for added custom fields/meta.
        foreach ($new_meta as $meta_key => $meta_value) {
            if (! isset($old_meta[$meta_key])) {
                error_log('meta key added: ' . $meta_key);
                $context["post_prev_meta_$meta_key"] = null;
                $context["post_new_meta_$meta_key"] = $meta_value;
            }
        }
    
        // Look for changed custom fields/meta.
        foreach ($old_meta as $meta_key => $meta_value) {
            if (isset($new_meta[$meta_key]) && json_encode($old_meta[$meta_key]) !== json_encode($new_meta[$meta_key])) {
                error_log('meta key changed: ' . $meta_key);
                $context["post_prev_meta_$meta_key"] = $meta_value;
                $context["post_new_meta_$meta_key"] = $new_meta[$meta_key];
            }
        }
    
        return $context;
    }
}

new SimpleHistory();
