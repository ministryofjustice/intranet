<?php

/**
 *
 * Register the Workplace taxonomy
 *
 * */
add_action('init', 'create_workplace_taxonomy');

function create_workplace_taxonomy()
{

    $labels = array(
        'name' => 'Workplaces',
        'singular_name' => 'Workplace',
        'menu_name' => 'Workplaces',
        'all_items' => 'All Workplaces',
        'parent_item' => 'Parent Workplace',
        'parent_item_colon' => 'Parent Workplace:',
        'new_item_name' => 'New Workplace Name',
        'add_new_item' => 'Add New Workplace',
        'edit_item' => 'Edit Workplace',
        'update_item' => 'Update Workplace',
        'separate_items_with_commas' => 'Separate Workplaces with commas',
        'search_items' => 'Search Workplaces',
        'add_or_remove_items' => 'Add or remove Workplace',
        'choose_from_most_used' => 'Choose from the most used Workplaces',
        'not_found' => 'Not Found',
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false
    );

    register_taxonomy('workplace', 'condolences', $args);

}