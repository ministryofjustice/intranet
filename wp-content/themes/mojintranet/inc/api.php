<?php

/**
* Add REST API support to an already registered post type.
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
 * Add REST API support to an already registered taxonomy.
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