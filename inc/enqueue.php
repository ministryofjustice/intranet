<?php

if (!defined('ABSPATH')) {
    die();
}

add_action( 'wp_enqueue_scripts','enqueue_core_script'  );

function enqueue_core_script(){

    wp_enqueue_script( 'core-js', get_stylesheet_directory_uri().'/assets/js/core.min.js?a=1.2.10' );
    wp_localize_script('core-js', 'myAjax', 
        array( 'ajaxurl' => admin_url('admin-ajax.php') )
    );
}

/**
 * Return the assets folder in the child theme
 */

function get_assets_folder()
{
    return get_stylesheet_directory_uri().'/assets';
}
