<?php

namespace MOJ_Intranet\List_Tables;

/**
 * Queries derived from a post listing.
 *
 * The admin filters a listing through several hooks: the agency filter in
 * Taxonomies\Agency, the region filter in Taxonomies\Region, and whatever
 * plugins add on top. Anything that needs to describe the same set of posts,
 * such as a status count or the months a date filter can offer, has to apply
 * all of them or it will disagree with the listing it sits above.
 *
 * Rather than restate those conditions, the listing's own WP_Query is built and
 * its SQL borrowed. The query is short circuited before it runs, so the caller
 * pays for its own aggregate and nothing else.
 */

class Listing_Query
{
    /**
     * Build the SQL a listing would run, without running it.
     *
     * @param string $post_type
     * @param string $fields The SELECT list for the inner query.
     * @param array $args Additional WP_Query arguments.
     * @return string SQL, already prepared. Do not pass it through wpdb::prepare().
     */
    public static function request($post_type, $fields, $args = [])
    {
        // posts_clauses_request is the later of the two rounds of clause
        // filters, and WP_Query re-reads the field list after each, so the list
        // is set in both.
        $select = function ($clauses) use ($fields) {
            $clauses['fields'] = $fields;
            return $clauses;
        };

        // Returning an array stops WP_Query hitting the database. The request
        // is already assembled by this point.
        $skip = function () {
            return [];
        };

        add_filter('posts_clauses', $select, PHP_INT_MAX);
        add_filter('posts_clauses_request', $select, PHP_INT_MAX);
        add_filter('posts_pre_query', $skip, PHP_INT_MAX);

        $query = new \WP_Query(array_merge([
            'post_type'              => $post_type,
            'posts_per_page'         => -1,
            'orderby'                => 'none',
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'cache_results'          => false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ], $args));

        remove_filter('posts_pre_query', $skip, PHP_INT_MAX);
        remove_filter('posts_clauses_request', $select, PHP_INT_MAX);
        remove_filter('posts_clauses', $select, PHP_INT_MAX);

        return $query->request;
    }

    /**
     * The months that contain posts for a listing, newest first.
     *
     * Every month returned is one the listing can actually show, which is what
     * stops a date filter offering a selection that comes back empty.
     *
     * An empty array is a real answer: the listing holds nothing. Failure is
     * reported as false instead, so a caller can tell "no months" from "could
     * not find out" and does not fall back to a wider list that would offer
     * dates the listing cannot show.
     *
     * @param string $post_type
     * @return object[]|false Rows with integer year and month properties, or false on failure.
     */
    public static function months($post_type)
    {
        global $wpdb;

        // Mirror the post_status handling in WP_List_Table::months_dropdown().
        if (isset($_GET['post_status']) && $_GET['post_status'] === 'trash') {
            $statuses = ['trash'];
        } else {
            $statuses = array_diff(get_post_stati(), ['auto-draft', 'trash']);
        }

        $request = self::request(
            $post_type,
            "{$wpdb->posts}.ID, {$wpdb->posts}.post_date",
            ['post_status' => array_values($statuses)]
        );

        if (empty($request)) {
            return false;
        }

        // $request is already prepared, so it is not passed through prepare().
        $results = $wpdb->get_results(
            "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
             FROM ($request) AS filtered
             ORDER BY year DESC, month DESC"
        );

        if ($wpdb->last_error) {
            return false;
        }

        // wpdb returns strings. Cast so rows compare cleanly with months built
        // elsewhere, such as Date_Range::with_requested_months().
        return array_map(function ($row) {
            return (object) [
                'year'  => (int) $row->year,
                'month' => (int) $row->month,
            ];
        }, (array) $results);
    }
}
