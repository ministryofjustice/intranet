<?php

namespace MOJ_Intranet\Taxonomies;

class Campaign_Category extends Content_Category {
    protected $name = 'campaign_category';

    protected $object_types = array(
        'news',
        'page',
        'post',
        'event'
    );

    protected $args = array(
        'labels' => array(
            'name' => 'Campaign Categories',
            'singular_name' => 'Campaign Category',
            'menu_name' => 'Campaign Categories',
            'all_items' => 'All Campaign Categories',
            'parent_item' => 'Parent Campaign Category',
            'parent_item_colon' => 'Parent Campaign Category:',
            'new_item_name' => 'New Campaign Category Name',
            'add_new_item' => 'Add New Campaign Category',
            'edit_item' => 'Edit Campaign Category',
            'update_item' => 'Update Campaign Category',
            'separate_items_with_commas' => 'Separate Campaign Categories with commas',
            'search_items' => 'Search Campaign Categories',
            'add_or_remove_items' => 'Add or remove Campaign Categories',
            'choose_from_most_used' => 'Choose from the most used Campaign Categories',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'rewrite' => false,
        'capabilities' => array(
            'manage_terms' => 'manage_campaign_categories',
            'edit_terms' => 'edit_campaign_categories',
            'delete_terms' => 'delete_campaign_categories',
            'assign_terms' => 'assign_campaign_categories',
        ),
    );
}
