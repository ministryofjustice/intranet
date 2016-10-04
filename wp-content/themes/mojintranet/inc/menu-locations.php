<?php

/**
 * Register menus for the theme.
 */

function dw_rename_primary_menu() {
    $menus['main-menu'] = 'Main Menu';

    register_nav_menus($menus);
}
add_action('init', 'dw_rename_primary_menu');
