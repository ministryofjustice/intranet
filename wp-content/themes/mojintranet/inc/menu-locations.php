<?php

/**
 * Register menus for the theme.
 */

function register_taxonomy_menus() {
    // This theme uses wp_nav_menu() in one location.
    $terms = get_terms('agency', array(
        'hide_empty' => false,
    ));

    $menus = array();

    foreach ($terms as $term) {
        $menu_slug = $term->slug . '-quick-links';
        $menu_name = $term->name . ' Quick Links';
        $menus[$menu_slug] = $menu_name;

        $menu_slug = $term->slug . '-guidance-index';
        $menu_name = $term->name . ' Guidance Index';
        $menus[$menu_slug] = $menu_name;
    }

    $menus['main-menu'] = 'Main Menu';

    register_nav_menus($menus);
}
add_action('init', 'register_taxonomy_menus');
