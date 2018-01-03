<?php

if (!defined('ABSPATH')) {
    die();
}

/***
 *
 * Search engine related functions
 *
 ***/
add_action('wp_enqueue_scripts', 'ajax_search_enqueues');

function ajax_search_enqueues()
{
    wp_enqueue_script('ajax-search', get_stylesheet_directory_uri() . '/tests/js-test/blog-content_filter.js', array( ), '1.1.45', true);
    wp_localize_script('ajax-search', 'myAjax', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
}

add_action('wp_ajax_load_search_results', 'load_search_results');
add_action('wp_ajax_nopriv_load_search_results', 'load_search_results');



function load_search_results()
{
  
    
}
