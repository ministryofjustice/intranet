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
        $menuSlug = $term->slug . '-quick-links';
        $menuName = $term->name . ' Quick Links';
        $menus[$menuSlug] = $menuName;
    }
    register_nav_menus($menus);
}
add_action('init', 'register_taxonomy_menus');
