<?php

namespace MOJ_Intranet\Taxonomies;

defined('ABSPATH') || die();

class OPG_Pillar extends Agency_Taxonomy
{
    protected $name = 'opg_pillar';

    protected $agency = 'opg';

    protected $object_types = array(
        'people-update',
    );

    protected $args = array(
        'labels' => array(
            'name' => 'Pillar',
            'singular_name' => 'Pillar',
            'menu_name' => 'Pillars',
            'all_items' => 'All Pillars',
            'new_item_name' => 'New Pillar Name',
            'add_new_item' => 'Add New Pillar',
            'edit_item' => 'Edit Pillar',
            'update_item' => 'Update Pillar',
            'separate_items_with_commas' => 'Separate Pillars with commas',
            'search_items' => 'Search Pillars',
            'add_or_remove_items' => 'Add or remove Pillars',
            'choose_from_most_used' => 'Choose from the most used Pillars',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => true,
        'show_tagcloud' => false,
        'rewrite' => false,
    );
}
