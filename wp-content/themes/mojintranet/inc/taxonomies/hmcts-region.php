<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;

class HMCTS_Region extends Agency_Taxonomy {
    protected $name = 'hmcts_region';

    protected $agency = 'hmcts';

    protected $object_types = array(
        'event'
    );

    protected $args = array(
        'labels' => array(
            'name' => 'HMCTS Regions',
            'singular_name' => 'Region',
            'menu_name' => 'HMCTS Regions',
            'all_items' => 'All Regions',
            'parent_item' => 'Parent Region',
            'parent_item_colon' => 'Parent Region:',
            'new_item_name' => 'New Region Name',
            'add_new_item' => 'Add New Region',
            'edit_item' => 'Edit Region',
            'update_item' => 'Update Region',
            'separate_items_with_commas' => 'Separate Regions with commas',
            'search_items' => 'Search Regions',
            'add_or_remove_items' => 'Add or remove Regions',
            'choose_from_most_used' => 'Choose from the most used Regions',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'rewrite' => false,

    );

    public function __construct() {
        parent::__construct();
    }








}
