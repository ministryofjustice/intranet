<?php

/**
 * CMS Tree Page View plugin customizations
 *
 * @package Clarity
 */

namespace MOJ\Intranet;

/**
 * Flush posts cache when CMS Tree Page View loads.
 * This ensures fresh menu_order values are displayed after reordering pages.
 */
add_action('cms_tree_page_view/before_wrapper', function () {
    // Try to flush just the posts group if supported, otherwise flush all
    if (function_exists('wp_cache_supports') && wp_cache_supports('flush_group')) {
        wp_cache_flush_group('posts');
    } elseif (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
});

/**
 * Flush posts cache after a page is moved via drag and drop.
 * This ensures subsequent requests get fresh menu_order values from the database.
 */
add_action('cms_tree_page_view_node_move_finish', function () {
    // Try to flush just the posts group if supported, otherwise flush all
    if (function_exists('wp_cache_supports') && wp_cache_supports('flush_group')) {
        wp_cache_flush_group('posts');
    } elseif (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
});
