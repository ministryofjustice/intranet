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
     * @return string Agency slug
     */
    public static function get_post_agency($post_id) {
        $terms = wp_get_object_terms($post_id, 'agency');
        $agencies = array_map(function($term) {
            return $term->slug;
        }, $terms);

        if (count($agencies) === 1) {
            $agency = $agencies[0];
        } else {
            $agency = 'hq';
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
     * Get the opt-in status of a post, given the agency.
     *
     * @param int $post_id
     * @param string $agency The agency slug (optional)
     * @return bool|null True/False for opted-in/out, respectively,
     *                   Null for not applicable
     *                     (i.e. the post cannot be opted-out of)
     */
    public static function is_post_opted_in($post_id, $agency = null) {
        $owner = self::get_post_agency($post_id);

        if (is_null($agency)) {
            $agency = Agency_Context::get_agency_context();
        }

        if ($owner !== 'hq') {
            // The post is not owned by HQ, so this post cannot be opted in/out.
            return null;
        } else {
            $opt_in = is_object_in_term($post_id, 'agency', $agency);
            return $opt_in;
        }
    }
}
