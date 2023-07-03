<?php
/* Register custom post types on the 'init' hook. */
add_action('init', 'clarity_regional_page_post_type');

/**
 * Registers the 'regional_page' post type
 *
 * @return void
 * @since  0.1.0
 * @access public
 */
function clarity_regional_page_post_type()
{
    $args = [
        'description' => __('This is a description for regional pages.', 'Clarity'),
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => false,
        'menu_icon' => 'dashicons-feedback',
        'delete_with_user' => true,
        'hierarchical' => true,
        'has_archive' => false,
        'query_var' => true,
        'capability_type' => 'regional_page',
        'map_meta_cap' => true,
        'rewrite' => [
            'slug' => 'regional-pages',
            'with_front' => false,
            'pages' => true,
            'feeds' => false
        ],
        'supports' => [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'revisions',
            'page-attributes'
        ],
        'labels' => [
            'name' => __('Regional pages', 'Clarity'),
            'singular_name' => __('Regional pages', 'Clarity'),
            'menu_name' => __('Regional pages', 'Clarity'),
            'name_admin_bar' => __('Pages', 'Clarity'),
            'add_new' => __('Add New', 'Clarity'),
            'add_new_item' => __('Add New page', 'Clarity'),
            'edit_item' => __('Edit page', 'Clarity'),
            'new_item' => __('New page', 'Clarity'),
            'view_item' => __('View regional page', 'Clarity'),
            'search_items' => __('Search pages', 'Clarity'),
            'not_found' => __('No pages found', 'Clarity'),
            'not_found_in_trash' => __('No pages found in trash', 'Clarity'),
            'all_items' => __('All pages', 'Clarity'),
            'parent_item' => __('Parent Page', 'Clarity'),
            'parent_item_colon' => __('Parent Page:', 'Clarity'),
            'archive_title' => __('Pages', 'Clarity')
        ],
    ];

    /* Register the post type. */
    register_post_type(
        'regional_page', // Post type name. Max of 20 characters. Uppercase and spaces not allowed.
        $args // Arguments for post type.
    );
}
