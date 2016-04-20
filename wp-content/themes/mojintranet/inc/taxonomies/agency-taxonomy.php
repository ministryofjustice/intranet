<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;

abstract class Agency_Taxonomy extends Taxonomy{

    /**
     * The agency the taxonomy belongs to
     * This should match the agency slug
     *
     * @var string
     */
    protected $agency = null;

    public function __construct() {
        parent::__construct();

        // Check submenus
        add_action( 'admin_menu', array($this, 'check_agency_taxonomy_submenu'), 999 );

        // Check metabox
        add_action( 'admin_menu', array($this, 'check_agency_taxonomy_metabox'), 999 );
    }

    /**
     *  Check Agency Taxonomy submenu item should be visible
     */
    public function check_agency_taxonomy_submenu() {

        $context = Agency_Context::get_agency_context();

        if ($this->agency != $context) {
            
            foreach($this->object_types as $object_type){

                if($object_type == 'post') {
                    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy='.$this->name);
                }
                else {
                    remove_submenu_page('edit.php?post_type=' . $object_type, 'edit-tags.php?taxonomy='.$this->name.'&amp;post_type=' . $object_type);
                }

            }

        }

    }

    /**
     *  Check Agency Taxonomy metabox
     */
    public function check_agency_taxonomy_metabox() {


        $context = Agency_Context::get_agency_context();

        if ($this->agency != $context) {
            foreach ($this->object_types as $object) {

                remove_meta_box($this->name . 'div', $object, 'normal');

            }
        }

    }

}
