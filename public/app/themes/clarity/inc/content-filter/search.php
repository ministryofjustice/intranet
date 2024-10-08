<?php

/**
 * FilterSearch
 * 
 * This class is responsible for handling the AJAX requests for the search filter.
 * 
 * @package Clarity
 */

namespace MOJ\Intranet;

use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;
use WP_Query;

class FilterSearch
{
    /**
     * FilterSearch constructor.
     * 
     * @return void
     */

    public function __construct()
    {
        $this->hooks();
    }

    /**
     * Hooks
     * 
     * @return void
     */
    public function hooks(): void
    {
        add_action('wp_ajax_load_search_results', [$this, 'loadSearchResults']);
        add_action('wp_ajax_nopriv_load_search_results', [$this, 'loadSearchResults']);
        add_action('wp_ajax_load_events_filter_results', [$this, 'loadEventSearchResults']);
        add_action('wp_ajax_nopriv_load_events_filter_results', [$this, 'loadEventSearchResults']);
    }

    /**
     * Load results for events.
     * 
     * @return void
     */

    public function loadEventSearchResults()
    {
        if (!wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
            exit('Access not allowed.');
        }

        $active_agency = (new Agency())->getCurrentAgency();
        $agency_term_id = $active_agency['wp_tag_id'];
        $date_filter = sanitize_text_field($_POST['valueSelected'] ?? 'all');
        $post_id = get_the_ID();
        $query = sanitize_text_field($_POST['query']);

        $filter_options = ['keyword_search' => $query];

        if ($date_filter != 'all') {
            $filter_options['date_filter'] = $date_filter;
        }

        $events_helper = new EventsHelper();

        if (isset($_POST['termID'])) {
            $tax_id = sanitize_text_field($_POST['termID']);

            $filter_options['region_filter'] = $tax_id;

            $events = $events_helper->get_events($agency_term_id, $filter_options);
        } else {
            $events = $events_helper->get_events($agency_term_id, $filter_options);
        }

        if ($events) {
            echo '<div class="data-type" data-type="event"></div>';

            foreach ($events as $key => $event) :
                $event_id = $event->ID;
                $post_url = $event->url;
                $event_title = $event->post_title;

                $start_date = $event->event_start_date;
                $end_date = $event->event_end_date;
                $start_time = $event->event_start_time;
                $end_time = $event->event_end_time;
                $location = $event->event_location;
                $date = $event->event_start_date;
                $day = date('l', strtotime($start_date));
                $month = date('M', strtotime($start_date));
                $year = date('Y', strtotime($start_date));
                $all_day = $event->event_allday;

                if ($all_day == true) {
                    $all_day = 'all_day';
                }

                echo '<div class="c-events-item-list">';

                include locate_template('src/components/c-calendar-icon/view.php');

                include locate_template('src/components/c-events-item-byline/view.php');

                echo '</div>';
            endforeach;
        } else {
            echo 'No events found during this date range :(';
        }
        die();
    }

    /**
     * Load results for post types except for events.
     * 
     * @return void
     */

    public function loadSearchResults()
    {
        if (!wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
            exit('Access not allowed.');
        }

        // Enable ElasticPress integration with AJAX requests.
        add_filter('ep_ajax_wp_query_integration', '__return_true');

        // Apply the weighting fields configuration to the query.
        add_filter('ep_enable_do_weighting', '__return_true');

        // Run a query based on generated query arguments.
        $query = new WP_Query($this->getQueryArgs());

        // Use output buffering to capture the HTML output.
        // This is necessary to get the html without refactoring the component's code.
        ob_start();
        echo '<div class="data-type" data-type="' . sanitize_text_field($_POST['postType']) . '"></div>';
        foreach ($query->posts as $post) {
            // $post is used in the included file.
            include locate_template('src/components/c-article-item/view-news-feed.php');
        }
        $result_html = ob_get_clean();

        // Get the pagination HTML.
        $pagination_html = $this->getPagination(
            sanitize_text_field($_POST['valueSelected']),
            sanitize_text_field($_POST['nextPageToRetrieve']),
            $query->max_num_pages
        );

        // Return the results as JSON.
        return wp_send_json([
            'results' =>  $result_html,
            'total' => $query->found_posts  . ' search results',
            'pagination' => $pagination_html
        ]);
    }

    /**
     * Get Query Args
     * 
     * @return array
     */

    function getQueryArgs()
    {
        // Get the active agency.
        $active_agency = (new Agency())->getCurrentAgency();

        // Pagination.
        $post_per_page = 10;
        $next_page_to_retrieve = sanitize_text_field($_POST['nextPageToRetrieve'] ?? '');
        $offset = $next_page_to_retrieve ? (($next_page_to_retrieve - 1) * $post_per_page) : 0;

        // Post type, with a cleanup of the value.
        $post_type = sanitize_text_field($_POST['postType'] ?? '');
        $post_type = $post_type === 'posts' ? 'post' : $post_type;

        // Is the request for a news category?
        $news_category_id = sanitize_text_field($_POST['newsCategoryValue'] ?? '');

        // Check if the post type is regional.
        $is_regional = $post_type === 'regional_news' ? true : false;

        $args = [
            'numberposts' => $post_per_page,
            'post_type' => $post_type,
            'post_status' => 'publish',
            'offset' => $offset,
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'agency',
                    'field' => 'term_id',
                    'terms' => $active_agency['wp_tag_id']
                ],
                // If the region is set add its ID to the taxonomy query
                ...($is_regional ? [
                    'taxonomy' => 'region',
                    'field' => 'region_id',
                    'terms' =>  $news_category_id,
                ] : []),
                // If the news category is set add its ID unless the query is regional, 
                // as it will have already been added to the tax query.
                ...(!empty($news_category_id) && !$is_regional ? [
                    'taxonomy' => 'news_category',
                    'field' => 'category_id',
                    'terms' =>  $news_category_id,
                ] : []),
            ]
        ];

        // Get the date filter value.
        $value_selected = sanitize_text_field($_POST['valueSelected'] ?? '');

        // Parse dates from the value selected.
        if (!empty($value_selected)) {
            preg_match('/&after=([^&]*)&before=([^&]*)/', $value_selected, $matches);
            $args['date_query'] = [
                'after' =>  date('Y-m-d', strtotime($matches[1])),
                'before' => date('Y-m-d', strtotime($matches[2])),
                'inclusive' => false,
            ];
        }

        // Get the search query.
        $query = sanitize_text_field($_POST['query'] ?? '');

        // If there is a search query, set the orderby to relevance.
        if (!empty($query)) {
            $args['orderby'] = 'relevance';
            $args['s'] = $query;
        }

        return $args;
    }

    /**
     * Get Pagination
     * 
     * @param string $selected
     * @param int|string $next
     * @param int $total
     * @return string
     */

    function getPagination(string $selected, int|string $next, int $total): string
    {
        $html = '';
        if ($next == $total) {
            $html .= '<span class="nomore-btn" data-date="' . $selected . '">';
            $html .= '<span class="c-pagination__main">No More Results</span>';
            $html .= '</span>';
        } elseif ($total <= 1) {
            $html .= '<button class="more-btn" data-page="' . $next . '" data-date="' . $selected . '">';
            $html .= '<span class="c-pagination__main">No More Results</span>';
            $html .= '<span class="c-pagination__count"> ' . $next . ' of 1</span>';
            $html .= '</button>';
        } else {
            $html .= '<button class="more-btn" data-page="' . $next . '" data-date="' . $selected . '">';
            $html .= '<span class="c-pagination__main"><span class="u-icon u-icon--circle-down"></span> Load Next 10 Results</span>';
            $html .= '<span class="c-pagination__count"> ' . $next . ' of ' . $total . '</span>';
            $html .= '</button>';
        }
        return $html;
    }
}

new FilterSearch();
