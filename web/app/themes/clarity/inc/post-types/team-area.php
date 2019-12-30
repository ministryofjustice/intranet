<?php

/**
 * Team area custom post type
 */

/***
 *
 * New option page for header banner - ACF options
 * https://www.advancedcustomfields.com/resources/acf_add_options_page/
 ***/
function teamarea_option_pages()
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(
            array(
                'page_title' => 'Team Area',
                'capability' => 'edit_team_blogs',
                'icon_url'   => 'dashicons-groups',
                'menu_title' => 'Team Area',
                'menu_slug'  => 'team-area',
                'redirect'   => false,
            )
        );
    }
}
add_action('init', 'teamarea_option_pages');

function hide_teamarea_from_editors()
{
    // creating functions post_remove for removing menu item
    global $wp_roles;

    $current_user = wp_get_current_user();
    $roles        = $current_user->roles;
    $role         = array_shift($roles);

    if ($role == 'agency-editor' || $role == 'regional-editor') {
        remove_menu_page('team-area');
    }
}
add_action('admin_menu', 'hide_teamarea_from_editors');

function custom_theme_setup()
{
     add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'custom_theme_setup');

// Register Custom Post Type
function custom_post_type()
{
    $post_types = [ 'news', 'blogs', 'events', 'pages', 'specialists' ];

    foreach ($post_types as $key => $value) {
        $value_cap_first = ucfirst($value);
        $remove_last     = substr_replace($value_cap_first, '', -1);

        if ($value === 'news') {
            $new_item = 'Add New ' . $value_cap_first . ' Story';
        } else {
            $new_item = 'Add New ' . $remove_last;
        }

        if ($value === 'events') {
            $support_items = array( 'title', 'editor' );
        } elseif ($value === 'pages') {
            $support_items = array( 'title', 'editor', 'page-attributes' );
        } else {
            $support_items = array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' );
        }

        register_post_type(
            'team_' . $value,
            array(
                'labels'                => array(
                    'name'               => __($value_cap_first),
                    'singular_name'      => __($remove_last),
                    'add_new'            => __('Add New', $remove_last),
                    'add_new_item'       => __($new_item),
                    'new_item'           => __('New ' . $remove_last),
                    'edit_item'          => __('Edit ' . $remove_last),
                    'view_item'          => __('View ' . $remove_last),
                    'all_items'          => __($value_cap_first),
                    'search_items'       => __('Search ' . $value_cap_first),
                    'parent_item_colon'  => __('Parent ' . $value_cap_first),
                    'not_found'          => __('No ' . $value . ' found'),
                    'not_found_in_trash' => __('No ' . $value . ' found in Trash.'),
                ),
                'public'                => true,
                'publicly_queryable'    => true,
                'has_archive'           => false,
                'taxonomies'            => array( 'agency', 'campaign_category', 'team' ),
                'hierarchical'          => true,
                'query_var'             => true,
                'rewrite'               => array(
                    'slug'       => 'team-' . $value,
                    'with_front' => false,
                ),
                'show_ui'               => true,
                'show_admin_column'     => true,
                'show_in_menu'          => 'team-area',
                'supports'              => $support_items,
                'show_in_rest'          => true,
                'capability_type'       => 'team_' . $value,
                'capabilities'          => array(
                    'publish_posts'       => 'publish_team_' . $value,
                    'edit_posts'          => 'edit_team_' . $value,
                    'edit_others_posts'   => 'edit_others_team_' . $value,
                    'read_private_posts'  => 'read_private_team_' . $value,
                    'edit_post'           => 'edit_team_' . $value,
                    'delete_post'         => 'delete_team_' . $value,
                    'delete_posts'        => 'delete_team_' . $value,
                    'delete_others_posts' => 'delete_team_' . $value,
                    'read_post'           => 'read_team_' . $value,
                ),
                'rest_base'             => 'team-' . $value,
                'rest_controller_class' => 'WP_REST_Posts_Controller',
            )
        );
    }
    flush_rewrite_rules();
}
add_action('init', 'custom_post_type');

// custom team name taxonomy
function teamname_taxonomy()
{
    $team_name       = 'teams';
    $value_cap_first = ucfirst($team_name);
    $remove_last     = substr_replace($value_cap_first, '', -1);

    $labels = array(
        'name'              => _x($value_cap_first, 'taxonomy general name'),
        'singular_name'     => _x($remove_last, 'taxonomy singular name'),
        'search_items'      => __('Search ' . $value_cap_first),
        'all_items'         => __('All ' . $value_cap_first),
        'parent_item'       => __('Parent ' . $remove_last),
        'parent_item_colon' => __('Parent ' . $remove_last . ':'),
        'edit_item'         => __('Edit ' . $remove_last),
        'update_item'       => __('Update ' . $remove_last),
        'add_new_item'      => __('Add New ' . $remove_last),
        'new_item_name'     => __('New ' . $remove_last . ' Name'),
        'menu_name'         => __($value_cap_first),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => $team_name ),
        'show_in_rest'          => true,
        'rest_base'             => $team_name,
        'rest_controller_class' => 'WP_REST_Terms_Controller',
    );

    $post_types = array( 'team_news', 'team_blogs', 'team_events', 'team_profiles', 'team_pages', 'team_specialists' );

    register_taxonomy($team_name, $post_types, $args);
}
