<?php

namespace MOJIntranet\Taxonomies;

class Agency extends Taxonomy {
    protected $name = 'agency';

    protected $objectType = array(
        'page',
    );

    protected $args = array(
        'labels'                     => array(
            'name'                       => 'Agencies',
            'singular_name'              => 'Agency',
            'menu_name'                  => 'Agencies',
            'all_items'                  => 'All Agencies',
            'parent_item'                => 'Parent Agency',
            'parent_item_colon'          => 'Parent Agency:',
            'new_item_name'              => 'New Agency Name',
            'add_new_item'               => 'Add New Agency',
            'edit_item'                  => 'Edit Agency',
            'update_item'                => 'Update Agency',
            'separate_items_with_commas' => 'Separate Agencies with commas',
            'search_items'               => 'Search Agencies',
            'add_or_remove_items'        => 'Add or remove Agencies',
            'choose_from_most_used'      => 'Choose from the most used Agencies',
            'not_found'                  => 'Not Found',
        ),
        'hierarchical'               => false,
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'rewrite'                    => false,
        'capabilities'               => array(
            'manage_terms'               => 'manage_agencies',
            'edit_terms'                 => 'manage_agencies',
            'delete_terms'               => 'manage_agencies',
            'assign_terms'               => 'assign_agencies_to_posts',
        ),
    );

    public function __construct()
    {
        parent::__construct();

        if (current_user_can('manage_agencies')) {
            add_action('admin_menu', array($this, 'addAdminMenuItem'));
        }
    }

    public function addAdminMenuItem()
    {
        add_submenu_page('users.php', 'Agencies', 'Agencies', 'administrator', 'edit-tags.php?taxonomy=agency&post_type=user');
    }
}
