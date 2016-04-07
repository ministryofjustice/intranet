<?php

/**
 * Helper methods to assist with tasks in the editor interface for Agency Editor users.
 *
 * Class AgencyEditor
 */

class AgencyEditor
{
    /**
     * Get the slug for the agency which the user
     * is currently in the context of.
     */
    public static function getCurrentAgencyContext() {
        return 'hmcts'; // @TODO: make this dynamic
    }

    /**
     * Get the agency / owner of a post.
     * Returns the WP_Term object of the agency.
     *
     * @param int $postID
     * @return WP_Term
     */
    public static function getPostAgency($postID) {
        $terms = wp_get_object_terms($postID, 'agency');
        $agencies = array_map(function($term) {
            return $term->slug;
        }, $terms);

        if (count($agencies) === 1) {
            $agency = self::getAgencyBySlug($agencies[0]);
        } else {
            $agency = self::getAgencyBySlug('hq');
        }

        return $agency;
    }

    /**
     * Retrieve an agency term by its slug.
     *
     * @param $slug
     * @return bool|WP_Term
     */
    public static function getAgencyBySlug($slug) {
        return get_term_by('slug', $slug, 'agency');
    }

    /**
     * Get the opt-out status of a post, given the agency.
     *
     * @param int $postID
     * @param string $agency The agency slug (optional)
     * @return bool
     */
    public static function isPostOptedOut($postID, $agency = null) {
        $owner = self::getPostAgency($postID);

        if (is_null($agency)) {
            $agency = self::getCurrentAgencyContext();
        }

        if ($owner->slug !== 'hq') {
            // The post is not owned by HQ, so 'opt-out' is not applicable here.
            return null;
        } else {
            $optIn = is_object_in_term($postID, 'agency', $agency);
            return !$optIn;
        }
    }
}