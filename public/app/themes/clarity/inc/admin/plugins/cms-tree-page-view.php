<?php

/**
 * CMS Tree Page View plugin customizations
 *
 * Handles cache flushing for Redis object cache compatibility.
 * The normalization logic for duplicate menu_order values is in the plugin itself.
 *
 * @package Clarity
 */

/**
 * Flush object cache when CMS Tree Page View loads.
 *
 * This ensures fresh menu_order values are displayed after reordering pages,
 * particularly important when using Redis or other persistent object caches.
 */
add_action('cms_tree_page_view/before_wrapper', function () {
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
});

/**
 * Flush object cache after a page is moved via drag and drop.
 *
 * This ensures subsequent requests get fresh menu_order values from the database.
 */
add_action('cms_tree_page_view_node_move_finish', function () {
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
});
