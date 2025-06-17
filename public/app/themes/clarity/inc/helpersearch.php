<?php
namespace MOJ\Intranet;

use WP_Query;

class HelperSearch
{

    private $options; // all search options
    private $meta_query; // stores SQL query parts responsible for checking the existence of meta data
    private $date_query;
    private $posts_orderby;

    public function __construct()
    {
        $this->debug = (bool) get_array_value($_GET, 'debug', false);
        $this->_reset();
    }

    private function _reset()
    {
        $this->options       = array();
        $this->meta_query    = array();
        $this->date_query    = array();
        $this->posts_orderby = '';
    }


    /** gets taxonomies based on url segments and agency cookie
     *
     * @param {Array} $options Options for WP Query
     * @return {Array} $options with added taxonomies
     */
    protected function add_taxonomies($options = array())
    {
        $agency             = get_intranet_code();
        $additional_filters = '';
        // ToDO: Check get_query_var to identify which variables we are passing? Only regions?
        // urldecode($this->get_param('additional_filters')) ?: '';
        $taxonomies = array( 'relation' => 'AND' );
        $filters    = array( 'agency=' . $agency );

        if (strlen($additional_filters)) {
            $filters = array_merge($filters, explode('&', $additional_filters));
        }

        foreach ($filters as $filter) {
            $pair     = explode('=', $filter);
            $taxonomy = $pair[0];
            $terms    = explode('|', $pair[1]);

            if (taxonomy_exists($taxonomy)) {
                $taxonomies[] = array(
                    'taxonomy' => $pair[0],
                    'field'    => 'slug',
                    'terms'    => $terms,
                );
            }
        }

        $options['tax_query'] = $taxonomies;

        return $options;
    }

    /** Get and format posts that meet the criteria from the options
     * The processing involves taking the raw WP query results and converting them into a clean array of results
     * See format_data() for details of processing each result
     *
     * @return Array of processed search results
     */
    public function get($options = array())
    {
        $data = $this->get_raw($options);
        $data = $this->format_data($data);

        return $data;
    }

    /** Get raw search results (as they come from WP query) that meet the criteria from $options
     *
     * @param {Array} $options Search criteria
     *    search_order - sorting direction (ASC or DESC) - only used when search_orderby is a string
     *    search_orderby - orderby field or array of key/value pairs representing fields and sort direction
     *    meta_fields - lists of meta fields used in the sql query
     *    post_type - post type or array of post types
     *    keywords - keywords
     *    page - page number
     *    per_page - how many results per page to show
     * @return {Array} an array containing raw results from WP query
     */
    public function get_raw($options = array())
    {
        static $data = array();

        $this->options = $this->add_taxonomies($options);
        $this->options = $this->normalize_options($this->options);
        $key           = json_encode($options); // json_encode is 5 times faster than serialize on average.

        // Try to reduce the number of times the database gets queried for the same information in a single call.
        if (! array_key_exists($key, $data)) {
            // process the query
            $data[ $key ]['raw']               = $this->get_raw_results();
            $data[ $key ]['total_results']     = (int) $data[ $key ]['raw']->found_posts;
            $data[ $key ]['retrieved_results'] = (int) $data[ $key ]['raw']->post_count;

            $this->_reset();
        }

        return $data[ $key ];
    }

    /** Normalize options by applying them on top of an array with default options
     *
     * @param {Array} $options Options
     * @return {Array} Normalized options
     */
    private function normalize_options($options)
    {
        $default = [
            'search_order'   => 'DESC',
            'search_orderby' => 'relevance',
            'meta_fields'    => [],
            'page'           => 1,
            'per_page'       => 10,
            'nopaging'       => false,
            'post_type'      => 'all',
            'keywords'       => '',
            'tax_query'      => [],
            'post__not_in'   => [],
            'post__in'       => [],
        ];

        foreach ($options as $key => $value) {
            if ($value) {
                $default[ $key ] = $value;
            }
        }

        $default['post_type'] = $this->convert_post_type($default['post_type']);

        return $default;
    }

    /** Converts pseudo post types to real types
     *
     * @param {String} $post_type Subject post type
     * @return {Array} Array of actual post types represented by the pseudo type
     */
    private function convert_post_type($post_type)
    {
        switch ($post_type) {
            case 'all':
                $post_type = array( 'page', 'regional_page', 'regional_news', 'news', 'document', 'webchat', 'event', 'post', 'team_news', 'note-from-antonia' );
                break;

            case 'content':
                $post_type = array( 'page', 'regional_page', 'regional_news', 'news', 'webchat', 'event', 'post', 'team_news', 'note-from-antonia' );
                break;
        }

        return $post_type;
    }

    /** Get the raw results straight from the WP query
     *
     * @return {Object} Results object returned by the WP query
     */
    private function get_raw_results()
    {
        $date_query = [];

        if (get_array_value($this->options, 'date', '')) {
            if (count($this->options['meta_fields'])) {
                $this->build_meta_clause();
                $this->build_date_query();
            } else {
                $date_query = $this->parse_date();
            }
        }

        $args = array(
            // Paging
            'nopaging'       => $this->options['nopaging'],
            'paged'          => $this->options['page'],
            'posts_per_page' => $this->options['per_page'],
            // Sorting
            'order'          => $this->options['search_order'],
            'orderby'        => $this->options['search_orderby'],
            // Filters
            'post_type'      => $this->options['post_type'],
            's'              => $this->rawurldecode($this->options['keywords']),
            'post__not_in'   => $this->options['post__not_in'],
            'post__in'       => $this->options['post__in'],
            'meta_query'     => $this->meta_query,
            'date_query'     => $date_query,
            'tax_query'      => $this->options['tax_query'],
        );

        add_filter('posts_orderby', array( $this, 'wp_filter_posts_orderby' ));
        $results = new WP_Query($args);
        remove_filter('posts_orderby', array( $this, 'wp_filter_posts_orderby' ));

        if (function_exists('relevanssi_do_query') && $this->options['keywords'] != null) {
            relevanssi_do_query($results);
        }

        return $results;
    }

    /** Format and trim the raw results
     *
     * @param {Object} $data Raw results object
     * @return {Array} Formatted results
     */
    private function format_data($data)
    {
        $data['results'] = array();

        foreach ($data['raw']->posts as $post) {
            $data['results'][] = $this->format_row($post);
        }

        unset($data['raw']);

        return $data;
    }

    /** Format a single results row
     *
     * @param {Object} $post Post object
     * @return {Array} Formatted and trimmed post
     */
    private function format_row($post)
    {
        $id = $post->ID;

        $titles         = array();
        $thumbnail      = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'thumbnail');
        $page_ancestors = get_post_ancestors($id);
        $excerpt        = $post->post_type == 'document' ? '' : $post->post_excerpt;

        if (count($page_ancestors)) {
            $titles = array_slice($page_ancestors, 0, 2);
            foreach ($titles as $index => $ancestor_id) {
                $titles[ $index ] = get_post_meta($ancestor_id, 'nav_label', true) ?: get_the_title($ancestor_id);
            }
            $titles = array_reverse($titles);
        } else {
            $titles[] = ucfirst($post->post_type);
        }

        return array(
            'id'                 => $id,
            'title'              => (string) get_the_title($id),
            'url'                => (string) get_the_permalink($id),
            'slug'               => (string) $post->post_name,
            'excerpt'            => (string) $excerpt,
            'thumbnail_url'      => (string) $thumbnail[0],
            'timestamp'          => (string) get_the_time('Y-m-d H:i:s', $id),
            'modified_timestamp' => (string) get_post_modified_time('Y-m-d H:i:s', false, $id),
            'file_url'           => (string) '',
            'file_name'          => (string) '',
            'file_size'          => (int) 0,
            'file_pages'         => (int) 0,
            'content_type'       => $titles,
            'relevance'          => $post->relevance_score,
        );
    }

    /** Build the meta clause for WP query
     */
    private function build_meta_clause()
    {
        if (count($this->options['meta_fields'])) {
            // foreach ($this->options['meta_fields'] as $meta_field) {
            // $this->meta_query[$meta_field] = array(
            // 'key' => $meta_field,
            // 'compare' => 'EXISTS'
            // );
            // }
            $mt_count            = 0;
            $posts_orderby_parts = array();

            foreach ($this->options['search_orderby'] as $field => $order) {
                if (in_array($field, $this->options['meta_fields'])) {
                    $mt_count++;
                    $posts_orderby_parts[] = 'mt' . $mt_count . '.meta_value' . ' ' . $order;
                } else {
                    $posts_orderby_parts[] = "'" . $field . "'" . ' ' . $order;
                }
            }

            if ($mt_count) {
                $this->posts_orderby = implode(', ', $posts_orderby_parts);
            }
        }
    }

    /** Parse the date from $options
     *
     * @return {Array} Date as an array of integer:
     * ['year'] => {Int},
     * ['monthnum'] => {Int},
     * ['day'] => {Int}
     */
    private function parse_date()
    {
        if (! $this->options['date']) {
            return;
        }

        $parts = explode('-', $this->options['date']);

        if (isset($parts[0])) {
            $date['year'] = $parts[0];
        }
        if (isset($parts[1])) {
            $date['monthnum'] = $parts[1];
        }
        if (isset($parts[2])) {
            $date['day'] = $parts[2];
        }

        return $date;
    }

    /** Build the date query for metadata for WP query
     */
    private function build_date_query()
    {
        $meta_query_or  = array( 'relation' => 'OR' );
        $meta_query_and = array( 'relation' => 'AND' );

        if (is_array($this->options['date'])) {
            // to be checked when rewriting the months API
            $compare = 'BETWEEN';
            if ($this->options['date'][0] == 'today') {
                $compare_value[] = date('Y-m-d');
            } else {
                $compare_value[] = $this->options['date'][0];
            }
            $compare_value[] = date('Y-m-t', strtotime('+' . $this->options['date'][1] . ' month'));
        } else {
            $compare = $this->options['date'] ? 'LIKE' : '>=';
            if ($this->options['date'] == 'today') {
                $compare       = '>=';
                $compare_value = date('Y-m-d');
            } else {
                $compare       = 'LIKE';
                $compare_value = $this->options['date'];
            }
        }

        foreach ($this->options['meta_fields'] as $meta_field) {
            $meta_query_or[]  = array(
                'key'     => $meta_field,
                'value'   => $compare_value,
                'type'    => 'date',
                'compare' => $compare,
            );
            $meta_query_and[] = array(
                'key' => $meta_field,
            );
        }

        $this->meta_query[] = array( $meta_query_or, $meta_query_and );
    }

    /** A wrapper for native rawurldecode
     */
    private function rawurldecode($string)
    {
        $string = str_replace('%252F', '%2F', $string);
        $string = str_replace('%255C', '%5C', $string);
        $string = rawurldecode($string);

        return $string;
    }

    public function wp_filter_posts_orderby($orderby)
    {
        if ($this->posts_orderby && $orderby) {
            $orderby = $this->posts_orderby;
        }
        return $orderby;
    }
}
