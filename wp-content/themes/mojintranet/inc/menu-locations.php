<?php

/**
 * Register menus for the theme.
 */

function register_taxonomy_menus() {
    $menus['main-menu'] = 'Main Menu';

    register_nav_menus($menus);
}
add_action('init', 'register_taxonomy_menus');
