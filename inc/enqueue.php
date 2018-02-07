<?php

if (!defined('ABSPATH')) {
    die();
}

add_action( 'wp_enqueue_scripts','enqueue_core_script'  );

function enqueue_core_script(){

    wp_enqueue_script( 'core-js', get_stylesheet_directory_uri() .'/assets/js/core.min.js?a=1.3.01' );
    wp_localize_script('core-js', 'myAjax',
        array( 'ajaxurl' => admin_url('admin-ajax.php') )
    );
}

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
add_action( 'admin_enqueue_scripts', 'clarity_custom_admin_style' );

function clarity_custom_admin_style() {
        wp_register_style( 'clarity_admin_css', get_stylesheet_directory_uri() . '/src/globals/css/admin/admin.css', false, '1.0.0' );
        wp_enqueue_style( 'clarity_admin_css' );
}

/**
 * Return the assets folder in the child theme
 */

function get_assets_folder()
{
    return get_stylesheet_directory_uri().'/assets';
}
