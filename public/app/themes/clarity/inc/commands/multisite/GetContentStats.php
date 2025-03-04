<?php

/**
 * This command is used to get content stats.
 * 
 * Usage:
 *  real-run: wp get-content-stats
 */

namespace MOJ\Intranet;

use WP_CLI;

class ContentStats
{
    public function __invoke($args): void
    {
        WP_CLI::log('ContentStats: starting');

        $taxonomy = get_taxonomy('agency');

        // Get all post types
        $post_types = $taxonomy->object_type;

        // Loop over each post type and get the stats
        foreach ($post_types as $key => $post_type) {
            $post_type_stats = $post_type === 'user' ?  $this->getUserStats() : $this->getPostTypeStats($post_type);
            WP_CLI::log('Agency term counts for ' . $post_type . ': ' . json_encode($post_type_stats, JSON_PRETTY_PRINT));
        }

        WP_CLI::log('ContentStats: complete');
    }

    public function getPostTypeStats($post_type)
    {
        $without_agency_terms = 0;
        $with_single_agency_terms = 0;
        $with_multiple_agency_terms = 0;
        $with_multiple_agency_terms_no_hq = 0;


        // Loop over every page and log out the slug
        $posts = get_posts(
            array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'number' => -1,
                'numberposts' => -1,
                'hierarchical' => 0,
            )
        );

        if (empty($posts)) {
            return [
                'total' => 0,
                'without_agency_terms' => 0,
                'with_multiple_agency_terms' => 0
            ];
        }

        foreach ($posts as $post) {

            // Get the agency terms for the page
            $agency_terms = wp_get_post_terms($post->ID, 'agency');

            if (count($agency_terms) === 1) {
                $with_single_agency_terms++;
                continue;
            }

            // If there are no agency terms, log that
            if (empty($agency_terms)) {
                $without_agency_terms++;
                continue;
            }

            // There are 2 or more agency terms
            $with_multiple_agency_terms++;

            // Check if there is an hq term
            $hq_term = array_filter($agency_terms, function ($term) {
                return $term->slug === 'hq';
            });

            if (count($hq_term) === 0) {
                $with_multiple_agency_terms_no_hq++;
            }
        }

        return [
            ...($without_agency_terms ? ['0' => $without_agency_terms] : []),
            ...($with_single_agency_terms ? ['1' => $with_single_agency_terms] : []),
            ...($with_multiple_agency_terms ? ['1+' => $with_multiple_agency_terms] : []),
            ...($with_multiple_agency_terms_no_hq ? ['1+ (no hq)' => $with_multiple_agency_terms_no_hq] : []),
            'total' => count($posts),
        ];
    }

    public function getUserStats()
    {
        $users = get_users();

        $counter_array = [];

        foreach ($users as $user) {
            // Get the user's role
            $user_roles = $user->roles;

            if(count($user_roles) > 1) {
                throw new \Exception('User has more than one role');
            }

            $user_role = empty($user_roles) ? 'no-role' : array_values($user_roles)[0];

            if(!isset($counter_array[$user_role])) {
                $counter_array[$user_role] = [
                    '0' => 0,
                    '1' => 0,
                    '1+' => 0,
                ];
            }

            $agency_terms = wp_get_object_terms($user->ID, 'agency');

            if (count($agency_terms) === 1) {
                $counter_array[$user_role]['1']++;
                continue;
            }

            if (empty($agency_terms)) {
                $counter_array[$user_role]['0']++;
                continue;
            }

            $counter_array[$user_role]['1+']++;
        }

        return $counter_array;
    }
}

// 1. Register the instance for the callable parameter.
$instance = new ContentStats();
WP_CLI::add_command('get-content-stats', $instance);

// 2. Register object as a function for the callable parameter.
WP_CLI::add_command('get-content-stats', 'MOJ\Intranet\ContentStats');
