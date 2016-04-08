<?php

/**
 * Class Agency_Context
 *
 * Helper class for working with an editor's agency context in the WordPress
 * admin area.
 */

class Agency_Context {
    /**
     * Can the current user have an agency editor context?
     *
     * @return bool
     */
    public static function current_user_can_have_context() {
        return current_user_can('edit_pages');
    }

    /**
     * Is the current user allowed to change context to the specified agency?
     *
     * @param string $agency Slug of the agency
     * @return array
     */
    public static function current_user_can_change_to($agency) {
        $available = self::current_user_available_agencies();

        // Extract slugs from array of WP_Term objects
        $available = array_map(function($term) {
            return $term->slug;
        }, $available);

        return in_array($agency, $available);
    }

    /**
     * Which agencies can the current user change context to?
     * Returns an array of WP_Term objects.
     *
     * @return WP_Term[]
     */
    public static function current_user_available_agencies() {
        if (current_user_can('agency-editor')) {
            $agencies = wp_get_object_terms(get_current_user_id(), 'agency');
        } elseif (current_user_can('assign_agencies_to_posts')) {
            $agencies = get_terms('agency', array(
                'hide_empty' => false,
            ));
        } else {
            $agencies = array();
        }

        return $agencies;
    }

    /**
     * Set the user's editor context
     *
     * @param $agency
     */
    public static function set_agency_context($agency) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'agency_context', $agency);
    }

    /**
     * Get the user's current editor context
     *
     * @return string
     */
    public static function get_agency_context() {
        $user_id = get_current_user_id();
        $agency = get_user_meta($user_id, 'agency_context', true);

        if (empty($agency)) {
            $available = self::current_user_available_agencies();
            $agency = array_shift($available);
            $agency = $agency->slug;
            self::set_agency_context($agency);
        }

        return $agency;
    }
}