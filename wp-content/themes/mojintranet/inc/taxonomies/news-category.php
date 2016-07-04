<?php

namespace MOJ_Intranet\Taxonomies;

class News_Category extends Content_Category {
    protected $name = 'news_category';

    protected $object_types = array(
        'news'
    );

    protected $args = array(
        'labels' => array(
            'name' => 'News Categories',
            'singular_name' => 'News Category',
            'menu_name' => 'News Categories',
            'all_items' => 'All News Categories',
            'parent_item' => 'Parent News Category',
            'parent_item_colon' => 'Parent News Category:',
            'new_item_name' => 'New News Category Name',
            'add_new_item' => 'Add New News Category',
            'edit_item' => 'Edit News Category',
            'update_item' => 'Update News Category',
            'separate_items_with_commas' => 'Separate News Categories with commas',
            'search_items' => 'Search News Categories',
            'add_or_remove_items' => 'Add or remove News Categories',
            'choose_from_most_used' => 'Choose from the most used News Categories',
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
            'manage_terms' => 'manage_news_categories',
            'edit_terms' => 'edit_news_categories',
            'delete_terms' => 'delete_news_categories',
            'assign_terms' => 'assign_news_categories',
        ),
    );
}
