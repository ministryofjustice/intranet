<?php

/**
 * FilterSearch
 * 
 * This class is responsible for handling the AJAX requests for the search filter.
 * 
 * @package Clarity
 */

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;
use MOJ\Intranet\SearchQueryArgs;
use WP_Query;

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

    public function loadEventSearchResults(): void
    {
        if (!wp_verify_nonce($_POST['_nonce'], 'search_filter_nonce')) {
            exit('Access not allowed.');
        }

        
        $agency_term_id =(new Agency())->getCurrentAgency()['wp_tag_id'];
        
        $filter_options = [
            'keyword_search' => sanitize_text_field($_POST['keywords_filter'] ?? ''),
            'date_filter' => $_POST['date_filter'] == 'all' ? '' : sanitize_text_field($_POST['date_filter']),
        ];
        
        
        if (isset($_POST['termID'])) {
            $tax_id = sanitize_text_field($_POST['termID']);
            
            $filter_options['region_filter'] = $tax_id;
        }

        $events_helper = new EventsHelper();

        $events = $events_helper->get_events($agency_term_id, $filter_options) ?? [];

        wp_send_json([
            'aggregates' => [
                'totalResults' =>  count($events),
                'resultsPerPage' => -1,
                'currentPage' => 1,
            ],
            'results' =>  [
                'templateName' => "c-events-item-list",
                'posts' => array_map([$this, 'mapEventResult'], $events),
            ]
        ]);
    }

    public function mapEventResult($event)
    {
        // Assign some default values.
        $time_formatted = 'All day';
        $datetime = 'P1D';

        if (!$event->event_allday) {
            $datetime = $event->event_start_time;
            // If start date and end date selected are the same, just display first date.
            if ($event->event_start_time === $event->event_end_time) {
                $time_formatted = substr($event->event_start_time, 0, 5);
            } else {
                $time_formatted = substr($event->event_start_time, 0, 5) . ' - ' . substr($event->event_end_time, 0, 5);
            }
        }

        if ($event->event_start_date === $event->event_end_date) {
            $multi_date = date('d M', strtotime($event->event_start_date));
        } else {
            $multi_date = date('d M', strtotime($event->event_start_date)) . ' - ' . date('d M', strtotime($event->event_end_date));
        }

        return [
            'permalink' => $event->url,
            'post_title' => $event->post_title,
            'year' => date('Y', strtotime($event->event_start_date)),
            'day' => date('l', strtotime($event->event_start_date)),
            'location' => $event->event_location,
            'time_formatted'  => $time_formatted,
            'datetime_formatted' => $datetime,
            'multi_date_formatted' => $multi_date
        ];
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

        $authors = new Authors();
        $authors = $authors->getAuthorInfo($post->ID);
        $author = $authors[0] ?? false;
        $author_display_name = $author['name'] ?? false;

        if (!$thumbnail) {
            $thumbnail = $author['thumbnail_url'] ?? false;
            $thumbnail_alt = $author_display_name;
        }

        return [
            'ID' => $post->ID,
            'post_type' => get_post_type($post->ID),
            'post_title' => $post->post_title,
            'post_date_formatted' => get_gmt_from_date($post->post_date, 'j M Y'),
            'post_excerpt_formatted' => empty($post->post_excerpt) ? '' : "<p>{$post->post_excerpt}</p>",
            'permalink' => get_permalink($post->ID),
            'post_thumbnail' => $thumbnail,
            'post_thumbnail_alt' => $thumbnail_alt,
            'author_display_name' => $author_display_name,
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

        if (is_page_template('page_events.php')) {
            get_template_part('src/components/c-events-item/view-list.ajax');
        }
    }
}
