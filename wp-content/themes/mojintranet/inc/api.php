<?php

/**
* Add REST API support to existing custom taxonomies.
*/
add_action( 'init', 'add_custom_post_type_rest_support', 25 );
function add_custom_post_type_rest_support() {
    global $wp_post_types;

    //Custom post types we want to have on the API

    $post_type_names = array (
        'guest_author',
        'document',
        'webchat',
        'event',
        'news',
        'regional_page',
        'regional_news'
    );
    foreach ($post_type_names as $post_type_name ) {
        if( isset( $wp_post_types[ $post_type_name ] ) ) {
            $wp_post_types[$post_type_name]->show_in_rest = true;
            // Optionally customize the rest_base or controller class
            $wp_post_types[$post_type_name]->rest_base = $post_type_name;
            $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
        }
    }

}

/**
 * Add REST API support to existing custom taxonomies.
 */
add_action( 'init', 'add_custom_taxonomy_rest_support', 25 );
function add_custom_taxonomy_rest_support() {
    global $wp_taxonomies;

    //be sure to set this to the name of your taxonomy!
    $taxonomy_names = array (
        'agency',
        'news_category',
        'resource_category',
        'campaign_category',
        'region'
    );

    foreach ($taxonomy_names as $taxonomy_name ) {
        if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
            $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;

            // Optionally customize the rest_base or controller class
            $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
            $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
        }
    }

}

/**
 * Add REST API endpoint for homepage news excluding featured
 *
 */
function get_homepage_news_endpoint(WP_REST_Request $request )
{
    $featured_ids = array ();

    $agency = $request->get_param( 'agency' );

    $agency = sanitize_text_field($agency);

    $max_news =  $request->get_param( 'max_news' );

    $a = 1;
    $featured = get_option($agency . '_featured_story' . $a );

    while ($featured)
    {
       array_push($featured_ids, $featured);
       $a++;
       $featured = get_option($agency . '_featured_story' . $a);
    }

    $options['tax_query'][0] = [
        'taxonomy' => 'agency',
        'field' => 'slug',
        'terms' => $agency,
    ];

    $args = array (
        // Paging
        'nopaging' => false,
        'offset' => 0,
        'posts_per_page' => $max_news,
        // Filters
        'post_type' => ['news'],
        'post__not_in' => $featured_ids,
        'tax_query' => $options['tax_query']
    );

    $news = get_posts($args);

    if ( empty( $news ) ) {
        return null;
    }

    return $news;
}

/**
 * Add REST API endpoint for homepage blogs excluding featured
 *
 */
function get_homepage_blogs_endpoint(WP_REST_Request $request )
{
    $featured_ids = array ();

    $agency = $request->get_param( 'agency' );

    $agency = sanitize_text_field($agency);

    $max_items =  $request->get_param( 'max_items' );

    $a = 1;
    $featured = get_option($agency . '_featured_story' . $a );

    while ($featured)
    {
        array_push($featured_ids, $featured);
        $a++;
        $featured = get_option($agency . '_featured_story' . $a);
    }

    $options['tax_query'][0] = [
        'taxonomy' => 'agency',
        'field' => 'slug',
        'terms' => $agency,
    ];

    $args = array (
        // Paging
        'nopaging' => false,
        'offset' => 0,
        'posts_per_page' => $max_items,
        // Filters
        'post_type' => ['post'],
        'post__not_in' => $featured_ids,
        'tax_query' => $options['tax_query']
    );

    $news = get_posts($args);

    if ( empty( $news ) ) {
        return null;
    }

    return $news;
}


/**
 *
 * Add REST API endpoint for featured news
 *
 */
function get_featured_news_endpoint(WP_REST_Request $request )
{
    $featured_ids = array ();

    $agency = $request->get_param( 'agency' );

    $agency = sanitize_text_field($agency);

    $max_featured =  $request->get_param( 'max_featured' );

    for($a = 1; $a <= $max_featured; $a++) {
        array_push($featured_ids, get_option($agency . '_featured_story' . $a));
    }

    $args = array (
        // Paging
        'nopaging' => false,
        'offset' => 0,
        'posts_per_page' => $max_featured,
        // Filters
        'post_type' =>  ['news', 'post', 'page'],
        'post__in' => $featured_ids,
        'orderby' => 'post__in'
    );

    $featured = get_posts($args);

    if ( empty( $featured ) ) {
        return null;
    }

    return $featured;
}

add_action( 'rest_api_init', function () {
    //Featured News
    register_rest_route( 'intranet/v1', '/featurednews/(?P<agency>[a-zA-Z0-9-]+)/(?P<max_featured>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_featured_news_endpoint',
    ) );

    //Homepage News
    register_rest_route( 'intranet/v1', '/homenews/(?P<agency>[a-zA-Z0-9-]+)/(?P<max_news>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_homepage_news_endpoint',
    ) );

    //Homepage Blog
    register_rest_route( 'intranet/v1', '/homebloglist/(?P<agency>[a-zA-Z0-9-]+)/(?P<max_items>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_homepage_blogs_endpoint',
    ) );

    //Events by Region
    register_rest_route( 'intranet/v1', '/events/(?P<agency>[a-zA-Z0-9-]+)/(?P<region>[a-zA-Z0-9-]+)/(?P<max_events>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_events_endpoint',
    ) );

    //Events
    register_rest_route( 'intranet/v1', '/events/(?P<agency>[a-zA-Z0-9-]+)/(?P<max_events>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_events_endpoint',
    ) );

} );


/*** ADD FILTER PARAMTER BACK INTO THE API **/
add_action( 'rest_api_init', 'rest_api_filter_add_filters' );


/**
 * Add the necessary filter to each post type
 **/
function rest_api_filter_add_filters() {
    foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
        add_filter( 'rest_' . $post_type->name . '_query', 'rest_api_filter_add_filter_param', 10, 2 );
    }
}
/**
 * Add the filter parameter
 *
 * @param  array           $args    The query arguments.
 * @param  WP_REST_Request $request Full details about the request.
 * @return array $args.
 **/
function rest_api_filter_add_filter_param( $args, $request ) {
    // Bail out if no filter parameter is set.
    if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
        return $args;
    }
    $filter = $request['filter'];

    if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100 ) ) {
        $args['post_per_page'] = $filter['posts_per_page'];
    }
    global $wp;


    $vars = apply_filters( 'rest_query_vars', $wp->public_query_vars );

    foreach ( $vars as $var ) {
        if ( isset( $filter[ $var ] ) ) {
            $args[ $var ] = $filter[ $var ];
        }
    }

    return $args;
}

//Add Meta variables to be visible
function intranet_allow_meta_query( $valid_vars ) {

    $valid_vars = array_merge( $valid_vars, array( 'meta_key', 'meta_value' ) );
    return $valid_vars;
}
add_filter( 'rest_query_vars', 'intranet_allow_meta_query' );

    foreach ($allowed_meta_fields as $posttype => $metas)
    {
        foreach ($metas as $meta_field)
        {
            register_rest_field( $posttype,
                $meta_field,
                array(
                    'get_callback'    => 'api_get_meta_value',
                    'update_callback' => null,
                    'schema'          => null,
                )
            );
        }
    }
}

/**
 * Get the value of the meta field to the API
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function api_get_meta_value( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name, true );
}


/**
 * Add Events endpoint to show future events by agency
 *
 */
function get_events_endpoint(WP_REST_Request $request )
{

    //Taxonomy queries: Agency and Regions
    $agency = sanitize_text_field($request->get_param( 'agency' ));

    $options['tax_query'] = array (
        'relation' => 'AND'
    );
    $options['tax_query'][0] = [
        'taxonomy' => 'agency',
        'field' => 'slug',
        'terms' => $agency,
    ];

    $region = $request->get_param( 'region' );

    if (!is_null($region))
    {
        $region = sanitize_text_field($region);
        $options['tax_query'][1] = [
            'taxonomy' => 'region',
            'field' => 'slug',
            'terms' => array ( $region ),
        ];
    }
    else {
        $term_slugs = get_term_slugs('region');
        $options['tax_query'][1] = [
            'taxonomy' => 'region',
            'field' => 'slug',
            'terms' => $term_slugs,
            'operator' => 'NOT IN'
        ];
    }

    //Pagination
    $options['page'] = 1;
    $options['per_page'] = $request->get_param( 'max_events' );

    //Order By
    $options['search_orderby'] = array(
        '_event-start-date' => 'ASC',
        '_event-end-date' => 'ASC',
        'title' => 'ASC'
    );

    //Get events that are for today onwards
    $options ['meta_query'] = array(
            array
            (
                'relation' => 'OR',
                 array (
                    'key' => '_event-start-date',
                    'value' => date('Y-m-d'),
                    'type' => 'date',
                    'compare' => '>='
                 ),
                 array (
                    'key' => '_event-end-date',
                    'value' => date('Y-m-d'),
                    'type' => 'date',
                    'compare' => '>='
                 ),
            )
        );

    $args = array (
        // Paging
        'nopaging' => false,
        'paged' =>  $options['page'] = 1,
        'offset' => 0,
        'posts_per_page' => $options['per_page'],
        // Filters
        'post_type' => ['event'],
        'orderby' => $options['search_orderby'],
        'meta_query' => $options['meta_query'],
        'tax_query' => $options['tax_query']
    );

    $events = get_posts($args);

    $i = 0;

    //print_r($events);
    foreach ($events as $event) {

        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }

    return $events;
}
