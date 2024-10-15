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
 * @property int $page - The page number.
 * @property int $posts_per_page - The number of posts per page.
 * @property ?bool $exclude_current - Exclude the current post.
 * @property ?string $keywords_filter - The keywords filter.
 * @property ?string $date_filter - The date filter.
 * @property ?string $news_category_id - The news category ID.
 * @property ?string $region_id - The region ID.
 *
 * @return void
 */

class SearchQueryArgs
{
    public function __construct(
        public string $agency_term_id,
        public string $post_type,
        public int $page,
        public int $posts_per_page = 10,
        public ?bool $exclude_current = false,
        public ?string $keywords_filter = null,
        public ?string $date_filter = null,
        public ?string $news_category_id = null,
        public ?string $region_id = null,
    ) {}

    function get()
    {
        // Pagination.
        $offset = $this->page ? (($this->page - 1) * $this->posts_per_page) : 0;

        $args = [
            'posts_per_page' => $this->posts_per_page,
            'post_type' => $this->post_type,
            'post_status' => 'publish',
            'offset' => $offset,
            ...($this->exclude_current ? ['post__not_in' => [get_the_ID()]] : []),
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'agency',
                    'field' => 'term_id',
                    'terms' => $this->agency_term_id
                ],
                // If the region is set add its ID to the taxonomy query
                ...(!empty($this->region_id) ? [
                    'taxonomy' => 'region',
                    'field' => 'region_id',
                    'terms' =>  $this->region_id,
                ] : []),
                // If the news category is set add its ID unless the query is regional, 
                // as it will have already been added to the tax query.
                ...(!empty($this->news_category_id) && empty($this->region_id) ? [
                    'taxonomy' => 'news_category',
                    'field' => 'category_id',
                    'terms' =>  $this->news_category_id,
                ] : []),
            ]
        ];

        // Parse dates from the date filter.
        if (!empty($this->date_filter)) {
            preg_match('/&after=([^&]*)&before=([^&]*)/', $this->date_filter, $matches);
            $args['date_query'] = [
                'after' =>  date('Y-m-d', strtotime($matches[1])),
                'before' => date('Y-m-d', strtotime($matches[2])),
                'inclusive' => false,
            ];
        }

        // If there is a search query, set the orderby to relevance.
        if (!empty($this->keywords_filter)) {
            $args['orderby'] = 'relevance';
            $args['s'] = $this->keywords_filter;
        }

        return $args;
    }
}

class Search
{

    /**
     * FilterSearch constructor.
     * 
     * @return void
     */

    public function __construct() {}

    /**
     * Hooks
     * 
     * @return void
     */
    public function hooks(): void
    {
        // Add functions to handle AJAX requests.
        add_action('wp_ajax_load_search_results', [$this, 'loadSearchResults']);
        add_action('wp_ajax_nopriv_load_search_results', [$this, 'loadSearchResults']);
        add_action('wp_ajax_load_events_filter_results', [$this, 'loadEventSearchResults']);
        add_action('wp_ajax_nopriv_load_events_filter_results', [$this, 'loadEventSearchResults']);

        // Add templates to the footer.
        add_action('wp_footer', [$this, 'addAjaxTemplates']);
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

    public function mapNewsResult(\WP_Post $post)
    {
        return [
            'ID' => $post->ID,
            'post_type' => get_post_type($post->ID),
            'post_title' => $post->post_title,
            'post_date_formatted' => get_gmt_from_date($post->post_date, 'j M Y'),
            'post_excerpt_formatted' => empty($post->post_excerpt) ? '' : "<p>{$post->post_excerpt}</p>",
            'permalink' => get_permalink($post->ID),
            'post_thumbnail' => get_the_post_thumbnail_url($post->ID, 'user-thumb'),
            'post_thumbnail_alt' => get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true),
        ];
    }

    public function mapPostResult(\WP_Post $post)
    {

        $thumbnail     = get_the_post_thumbnail_url($post->ID, 'user-thumb');
        $thumbnail_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);

        $author = $post->post_author;
        $author_display_name = $author ? get_the_author_meta('display_name', $author) : '';

        if (!$thumbnail) {
            // Mutate thumbnail with author image.
            $thumbnail = $author ? get_the_author_meta('thumbnail_avatar', $author) : false;
            $thumbnail_alt = $author_display_name;
        }

        return [
            'ID' => $post->ID,
            'post_type' => get_post_type($post->ID),
            'post_title' => $post->post_title,
            'post_date_formatted' => get_gmt_from_date($post->post_date, 'j M Y'),
            'post_excerpt_formatted' => empty($post->post_excerpt) ? '' : "<p>{$post->post_excerpt}</p>",
            'permalink' => get_permalink($post->ID),
            // 'post_thumbnail' => $thumbnail,
            'post_thumbnail' => false,
            'post_thumbnail_alt' => $thumbnail_alt,
            'author_display_name' => $author ? get_the_author_meta('display_name', $author) : '',
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
        if ($page < 1 || $page > 1000) {
            $page = 1;
        }

        $posts_per_page = (int) ($_POST['posts_per_page'] ?? 10);
        if ($posts_per_page < 1 || $posts_per_page > 100) {
            $posts_per_page = 10;
        }

        $allowed_post_types = ['post', 'news'];

        if (!in_array($_POST['post_type'], $allowed_post_types)) {
            throw new \Exception('Invalid post type.');
        }

        $post_type = $_POST['post_type'];

        $query_args = new SearchQueryArgs(
            (new Agency())->getCurrentAgency()['wp_tag_id'],
            $post_type,
            $page,
            $posts_per_page,
            false,
            sanitize_text_field($_POST['keywords_filter'] ?? null),
            sanitize_text_field($_POST['date_filter'] ?? null),
            sanitize_text_field($_POST['news_category_id'] ?? null),
            sanitize_text_field($_POST['region_id'] ?? null)
        );

        // Run a query based on generated query arguments.
        $query = new WP_Query($query_args->get());

        $map_function = $post_type === 'news' ? 'mapNewsResult' : 'mapPostResult';

        return wp_send_json([
            'aggregates' => [
                'totalResults' =>  $query->found_posts,
                'resultsPerPage' => $posts_per_page,
                'currentPage' => $page,
            ],
            'results' =>  [
                'posts' => array_map([$this, $map_function], $query->posts),
                'templateName' => "view-{$post_type}-feed",
            ],
        ]);
    }


    /**
     * Add AJAX templates to the footer.
     * 
     * These JS templates are used to render the AJAX results to html.
     * 
     * @return void
     */

    public function addAjaxTemplates()
    {

        if (is_page_template('page_blog.php') || is_page_template('page_news.php')) {
            get_template_part('src/components/c-pagination/view-infinite.ajax');
            echo '<script src="https://cdn.jsdelivr.net/gh/ranaroussi/pointjs/dist/point.js"></script>';
        }

        if (is_page_template('page_blog.php')) {
            get_template_part('src/components/c-article-item/view-blog-feed.ajax');
        }

        if (is_page_template('page_news.php')) {
            get_template_part('src/components/c-article-item/view-news-feed.ajax');
        }
    }
}
