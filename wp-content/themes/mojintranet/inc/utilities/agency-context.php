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
        return current_user_can('edit_pages') || current_user_can('edit_regional_pages');
    }

    /**
     * Is the current user allowed to change context to the specified agency?
     *
     * @param string $agency Slug of the agency
     * @return bool
     */
    public static function current_user_can_change_to($agency) {
        $available = self::current_user_available_agencies();
        return in_array($agency, $available);
    }

    /**
     * Which agencies can the current user change context to?
     * Returns an array of agency slugs.
     *
     * @return array
     */
    public static function current_user_available_agencies() {
        if (current_user_can('assign_agencies_to_posts')) {
            $agencies = get_terms('agency', array(
                'hide_empty' => false,
            ));
        }
        else {
            $agencies = wp_get_object_terms(get_current_user_id(), 'agency');
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
     * @param $agency
     * @return bool|WP_Error
     */
    public static function set_agency_context($agency) {
        if (self::current_user_can_change_to($agency)) {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'agency_context', $agency);
        } else {
            return new \WP_Error('unauthorised_context', "You cannot change context to the agency '$agency'.");
        }
    }

    /**
     * Get the user's current editor context.
     * If the user doesn't have a context, default to one of the users'
     * available agencies, with a preference for HQ.
     *
     * @param string $return_field The field that you want returned for the current agency. Default: slug
     * @return string The requested field value for the current agency
     */
    public static function get_agency_context($return_field = 'slug') {
        $user_id = get_current_user_id();
        $agency = get_user_meta($user_id, 'agency_context', true);
        $available = self::current_user_available_agencies();

        // If current context is empty or invalid, set one.
        if (empty($agency) || !self::current_user_can_change_to($agency)) {
            // Prefer HQ agency, else pick the first available agency.
            if (in_array('hq', $available)) {
                $agency = 'hq';
            } else {
                $agency = array_shift($available);
            }

            self::set_agency_context($agency);
        }

        if ($return_field == 'slug') {
          return $agency;
        } else {
          $context_term = get_term_by('slug', $agency, 'agency');
          return $context_term->{$return_field};
        }
    }
}
