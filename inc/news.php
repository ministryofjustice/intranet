<?php
namespace MOJ\Intranet;

/**
 * Retrieves News related data
 * Author: Irune Itoiz
 */

class News
{
    /**
     * Get featured news by agency
     *
     * @param string $agency
     */
    static function getFeaturedNews($agency = 'hq')
    {

        //Get the featured IDs for the agency
        $featured_ids = array();

        for($a = 1; $a <= MAX_FEATURED_NEWS; $a++) {
            array_push($featured_ids, get_option($agency . '_featured_story' . $a));
        }

        $args = [
            // Paging
            'nopaging' => false,
            'offset' => 0,
            'posts_per_page' => MAX_FEATURED_NEWS,
            // Filters
            'post_type' => ['news', 'post', 'page'],
            'post__in' => $featured_ids,
            'orderby' => 'post__in'
        ];

        return new \WP_Query($args);

    }

}