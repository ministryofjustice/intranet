<?php

if (!defined('ABSPATH')) {
    die();
}

add_action( 'wp_enqueue_scripts','enqueue_core_script'  );

function enqueue_core_script(){

    wp_enqueue_script( 'core-js', get_stylesheet_directory_uri().'/assets/js/core.min.js' );
}

/**
 * Return the assets folder in the child theme
 */

function get_assets_folder()
{
    return get_stylesheet_directory_uri().'/assets';
}
