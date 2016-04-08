<?php

/**
 * Helper methods to assist with tasks in the editor interface for Agency Editor users.
 *
 * Class AgencyEditor
 */

class Agency_Editor {
    /**
     * Get the agency / owner of a post.
     * Returns the WP_Term object of the agency.
     *
     * @param int $post_id
     * @return WP_Term
     */
    public static function get_post_agency($post_id) {
        $terms = wp_get_object_terms($post_id, 'agency');
        $agencies = array_map(function($term) {
            return $term->slug;
        }, $terms);

        if (count($agencies) === 1) {
            $agency = self::get_agency_by_slug($agencies[0]);
        } else {
            $agency = self::get_agency_by_slug('hq');
        }

        return $agency;
    }

    /**
     * Retrieve an agency term by its slug.
     *
     * @param $slug
     * @return bool|WP_Term
     */
    public static function get_agency_by_slug($slug) {
        return get_term_by('slug', $slug, 'agency');
    }

    /**
     * Get the opt-out status of a post, given the agency.
     *
     * @param int $post_id
     * @param string $agency The agency slug (optional)
     * @return bool
     */
    public static function is_post_opted_out($post_id, $agency = null) {
        $owner = self::get_post_agency($post_id);

        if (is_null($agency)) {
            $agency = Agency_Context::get_agency_context();
        }

        if ($owner->slug !== 'hq') {
            // The post is not owned by HQ, so 'opt-out' is not applicable here.
            return null;
        } else {
            $opt_in = is_object_in_term($post_id, 'agency', $agency);
            return !$opt_in;
        }
    }
}
