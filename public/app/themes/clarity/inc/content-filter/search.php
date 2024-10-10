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


/**
 * QueryProps
 * 
 * This class is responsible for handling the query properties.
 * 
 * @package Clarity
 *
 * @property string $agency - The active agency.
 * @property string $post_type - The post type.
 * @property string $page - The page number.
 * @property string $posts_per_page - The number of posts per page.
 * @property string $keywords_filter - The keywords filter.
 * @property string $date_filter - The date filter.
 * 
 * @return void
 */

class QueryProps
{
    public $agency_term_id;
    public $post_type;
    public $page;
    public $posts_per_page;
    public $keywords_filter;
    public $date_filter;
    public $news_category_id;
    public $region_id;

    public function __construct(
        $agency_term_id,
        $post_type,
        $page,
        $posts_per_page = 10,
        $keywords_filter = null,
        $date_filter = null,
        $news_category_id = null,
        $region_id = null
    ) {
        $this->agency_term_id = $agency_term_id;
        $this->post_type = $post_type;
        $this->page = $page;
        $this->posts_per_page = $posts_per_page;
        $this->date_filter = $date_filter;
        $this->keywords_filter = $keywords_filter;
        $this->news_category_id = $news_category_id;
        $this->region_id = $region_id;
    }
}

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

    public function mapResults(\WP_Post $post)
    {
        return [
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'post_date_formatted' => get_gmt_from_date($post->post_date, 'j M Y'),
            'post_excerpt_formatted' => empty($post->post_excerpt) ? '' : "<p>{$post->post_excerpt}</p>",
            'permalink' => get_permalink($post->ID),
            'post_type' => get_post_type($post->ID), // ? Is not used in the template.
            'post_thumbnail' => get_the_post_thumbnail_url($post->ID, 'user-thumb'),
            'post_thumbnail_alt' => get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true),
        ];
    }

    /**
     * Load results for post types except for events.
     * 
     * @return void
     */

    public function loadSearchResults()
    {
        if (!wp_verify_nonce($_POST['_nonce'], 'search_filter_nonce')) {
            exit('Access not allowed.');
        }

        // Enable ElasticPress integration with AJAX requests.
        add_filter('ep_ajax_wp_query_integration', '__return_true');

        // Apply the weighting fields configuration to the query.
        add_filter('ep_enable_do_weighting', '__return_true');

        $page = (int) $_POST['page'] ?? 1;
        if($page < 1 || $page > 1000) {
            $page = 1;
        }

        $posts_per_page = (int) ($_POST['posts_per_page'] ?? 10);
        if($posts_per_page < 1 || $posts_per_page > 100) {
            $posts_per_page = 10;
        }

        $query_props = new QueryProps(
            (new Agency())->getCurrentAgency()['wp_tag_id'],
            sanitize_text_field($_POST['post_type']),
            $page,
            $posts_per_page,
            sanitize_text_field($_POST['keywords_filter'] ?? null),
            sanitize_text_field($_POST['date_filter'] ?? null),
            sanitize_text_field($_POST['news_category_id'] ?? null),
            sanitize_text_field($_POST['region_id'] ?? null)
        );

        // Run a query based on generated query arguments.
        $query = new WP_Query($this->getQueryArgs($query_props));

        // include locate_template('src/components/c-article-item/view-news-feed.php');

        return wp_send_json([
            'aggregates' => [
                'totalResults' =>  $query->found_posts,
                'resultsPerPage' => $posts_per_page,
                'currentPage' => $page,
            ],
            'results' =>  array_map([$this, 'mapResults'], $query->posts),
        ]);

    }

    /**
     * Get Query Args
     * 
     * @return array
     */

    function getQueryArgs(QueryProps $props)
    {
        // Pagination.
        $offset = $props->page ? (($props->page - 1) * $props->posts_per_page) : 0;

        $args = [
            'numberposts' => $props->posts_per_page,
            'post_type' => $props->post_type,
            'post_status' => 'publish',
            'offset' => $offset,
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'agency',
                    'field' => 'term_id',
                    'terms' => $props->agency_term_id
                ],
                // If the region is set add its ID to the taxonomy query
                ...(!empty($props->region_id) ? [
                    'taxonomy' => 'region',
                    'field' => 'region_id',
                    'terms' =>  $props->region_id,
                ] : []),
                // If the news category is set add its ID unless the query is regional, 
                // as it will have already been added to the tax query.
                ...(!empty($props->news_category_id) && empty($props->region_id) ? [
                    'taxonomy' => 'news_category',
                    'field' => 'category_id',
                    'terms' =>  $props->news_category_id,
                ] : []),
            ]
        ];

        // Parse dates from the date filter.
        if (!empty($props->date_filter)) {
            preg_match('/&after=([^&]*)&before=([^&]*)/', $props->date_filter, $matches);
            $args['date_query'] = [
                'after' =>  date('Y-m-d', strtotime($matches[1])),
                'before' => date('Y-m-d', strtotime($matches[2])),
                'inclusive' => false,
            ];
        }

        // If there is a search query, set the orderby to relevance.
        if (!empty($props->keywords_filter)) {
            $args['orderby'] = 'relevance';
            $args['s'] = $props->keywords_filter;
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
