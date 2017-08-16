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
 * Add REST API endpoint
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
        'post_type' => ['news', 'post', 'page'],
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
    register_rest_route( 'intranet/v1', '/featurednews/(?P<agency>[a-zA-Z0-9-]+)/(?P<max_featured>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_featured_news_endpoint',
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

    $vars = apply_filters( 'query_vars', $wp->public_query_vars );
    print_r($vars);
    foreach ( $vars as $var ) {
        if ( isset( $filter[ $var ] ) ) {
            $args[ $var ] = $filter[ $var ];
        }
    }
    print_r($args);
    return $args;
}