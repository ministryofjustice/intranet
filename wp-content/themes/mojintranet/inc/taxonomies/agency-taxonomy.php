<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;

abstract class Agency_Taxonomy extends Taxonomy {

    /**
     * The agency the taxonomy belongs to
     * This should match the agency slug
     *
     * @var string
     */
    protected $agency = null;

    public function __construct() {
        parent::__construct();

        add_action('admin_menu', array($this, 'hide_submenu_based_on_context'), 999);
        add_action('admin_menu', array($this, 'hide_metabox_based_on_context'), 999);
    }

    /**
     *  Hides the Agency Taxonomy Submenu if the Taxonomy does not belong to the current agency context
     */
    public function hide_submenu_based_on_context() {
        $context = Agency_Context::get_agency_context();

        if ($this->agency != $context) {
            foreach ($this->object_types as $object_type) {
                if ($object_type == 'post') {
                    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=' . $this->name);
                }
                else {
                    remove_submenu_page('edit.php?post_type=' . $object_type, 'edit-tags.php?taxonomy=' . $this->name.'&amp;post_type=' . $object_type);
                }
            }
        }
    }

    /**
     *  Hides the Agency Taxonomy Metabox if the Taxonomy does not belong to the current agency context
     */
    public function hide_metabox_based_on_context() {
        $context = Agency_Context::get_agency_context();

        if ($this->agency != $context) {
            foreach ($this->object_types as $object) {
                remove_meta_box($this->name . 'div', $object, 'normal');
            }
        }
    }
}
