<?php
namespace MOJ\Intranet;
/**
 * Class Region_Context
 *
 * Helper class for working with an editor's region context in the WordPress
 * admin area.
 */

class Region_Context {
    /**
     * Can the current user have an region context?
     *
     * @return bool
     */
    public static function current_user_can_have_context() {
        return current_user_can('regional-editor');
    }

    /**
     * Is the current user allowed to change context to the specified region?
     *
     * @param string $region Slug of the region
     * @return bool
     */
    public static function current_user_can_change_to($region) {
        $available = self::current_user_available_regions();
        return in_array($region, $available);
    }

    /**
     * Which regions can the current user change context to?
     * Returns an array of region slugs.
     *
     * @return array
     */
    public static function current_user_available_regions() {
        if (current_user_can('administrator')) { //is admin
            $agencies = get_terms('region', array(
                'hide_empty' => false,
            ));
        }
        else {
            $agencies = wp_get_object_terms(get_current_user_id(), 'region');
        }

        // Create an array of slugs from the agency objects
        $slugs = array_map(function($term) {
            return $term->slug;
        }, $agencies);

        return $slugs;
    }

    /**
     * Set the user's editor context
     *
     * @param $region
     * @return bool|WP_Error
     */
    public static function set_region_context($region) {
        if (self::current_user_can_change_to($region)) {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'region_context', $region);
        } else {
            return new \WP_Error('unauthorised_context', "You cannot change context to the region '$region'.");
        }
    }

    /**
     * Get the user's current editor context.
     * If the user doesn't have a context, default to one of the users' available regions
     *
     * @param string $return_field The field that you want returned for the current region. Default: slug
     * @return string The requested field value for the current region
     */
    public static function get_region_context($return_field = 'slug') {
        $user_id = get_current_user_id();
        $region = get_user_meta($user_id, 'region_context', true);
        $available = self::current_user_available_regions();

        // If current context is empty or invalid, set one.
        if (empty($region) || !self::current_user_can_change_to($region)) {
            $region = array_shift($available);
            self::set_region_context($region);
        }

        if ($return_field == 'slug') {
          return $region;
        } else {
          $context_term = get_term_by('slug', $region, 'region');
          return $context_term->{$return_field};
        }
    }
}
