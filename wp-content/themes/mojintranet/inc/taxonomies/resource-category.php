<?php

namespace MOJ_Intranet\Taxonomies;

class Resource_Category extends Content_Category {
    protected $name = 'resource_category';

    protected $object_types = array(
        'page',
        'document',
        'regional_page'
    );

    protected $args = array(
        'labels' => array(
            'name' => 'Resource Categories',
            'singular_name' => 'Resource Category',
            'menu_name' => 'Resource Categories',
            'all_items' => 'All Resource Categories',
            'parent_item' => 'Parent Resource Category',
            'parent_item_colon' => 'Parent Resource Category:',
            'new_item_name' => 'New Resource Category Name',
            'add_new_item' => 'Add New Resource Category',
            'edit_item' => 'Edit Resource Category',
            'update_item' => 'Update Resource Category',
            'separate_items_with_commas' => 'Separate Resource Categories with commas',
            'search_items' => 'Search Resource Categories',
            'add_or_remove_items' => 'Add or remove Resource Categories',
            'choose_from_most_used' => 'Choose from the most used Resource Categories',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'rewrite' => false,
        'capabilities' => array(
            'manage_terms' => 'manage_resource_categories',
            'edit_terms' => 'edit_resource_categories',
            'delete_terms' => 'delete_resource_categories',
            'assign_terms' => 'assign_resource_categories',
        ),
    );
}
