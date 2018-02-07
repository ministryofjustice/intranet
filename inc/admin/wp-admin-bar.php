<?php

if (!defined('ABSPATH')) {
    die();
}

/**
* Initialise WP admin Toolbar
* https://codex.wordpress.org/Toolbar
* Also on parent theme but required here for toolbar to show up on new theme.
*/
add_action('init', '_wp_admin_bar_init');
