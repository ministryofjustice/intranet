<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;

class Region extends Taxonomy {
    protected $name = 'region';

    protected $object_types = array(
        'news',
        'post',
        'event'
    );

    protected $args = array(
        'labels' => array(
            'name' => 'Regions',
            'singular_name' => 'Region',
            'menu_name' => 'Regions',
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

        // Remove region meta boxes
        add_action('admin_menu', array($this, 'remove_region_meta_boxes'), 998);

        // Remove region submenus
        add_action( 'admin_menu', array($this, 'remove_region_submenus'), 999 );
    }

    /**
     * Register the taxonomy with WordPress.
     */
    public function register() {

        $agencies = get_terms( 'agency', array('hide_empty' => false));

        foreach ($agencies as $agency) {

            $this->name = $agency->slug.'_region';
            $this->args['labels']['name'] = $agency->name . ' Regions';
            $this->args['labels']['menu_name'] = $agency->name . ' Regions';
            parent::register();

        }

    }

    /**
     * Remove region meta box from post edit pages depending on current context
     */
    public function remove_region_meta_boxes() {

        $agencies = get_terms( 'agency', array('hide_empty' => false));
        $context = Agency_Context::get_agency_context();

        if ($context != 'hq') {
            foreach ($this->object_types as $object) {

                foreach ($agencies as $agency) {

                    if($agency->slug != $context) {
                        remove_meta_box($agency->slug . '_regiondiv', $object, 'normal');
                    }
                }

            }
        }
    }


    /**
     * Remove region meta box from post edit pages depending on current context
     */
    public function remove_region_submenus() {

        $agencies = get_terms( 'agency', array('hide_empty' => false));
        $context = Agency_Context::get_agency_context();


        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=hq_region&post_type=event' );

        foreach ($agencies as $agency) {

                    if($agency->slug != $context) {

                        remove_submenu_page( 'edit.php?post_type=news', 'edit-tags.php?taxonomy='.$agency->slug.'_region' );
                    }
        }



    }






}
