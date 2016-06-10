<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;

class Content_Category extends Taxonomy {
    public function __construct() {
        parent::__construct();

        $this->set_role_permissions();

        add_action('admin_menu', array($this, 'remove_default_meta_box'));
        add_action('add_meta_boxes', array($this, 'add_custom_category_meta_box'));
    }

    /**
     * Set Category Permissions for Adminstrator, Global Editor and Agency Editor
     */
    public function set_role_permissions() {
        $administrator = get_role('administrator');

        if (array_key_exists($this->args['capabilities']['assign_terms'], $administrator->capabilities) == false) {
            $administrator->add_cap($this->args['capabilities']['manage_terms']);
            $administrator->add_cap($this->args['capabilities']['edit_terms']);
            $administrator->add_cap($this->args['capabilities']['delete_terms']);
            $administrator->add_cap($this->args['capabilities']['assign_terms']);

            $editor = get_role('editor');
            $editor->add_cap($this->args['capabilities']['assign_terms']);

            $agency_editor = get_role('agency-editor');
            $agency_editor->add_cap($this->args['capabilities']['assign_terms']);
        }
    }

    /**
     * Check if any terms in the category relate to the current Agency context
     *
     * @return bool
     */
    public function context_has_terms() {
        $terms = get_terms($this->name, array('hide_empty' => 0));
        $context = Agency_Context::get_agency_context('term_id');

        foreach ($terms as $term){
            $term_agencies = get_field('term_used_by', $this->name . '_' . $term->term_id);

            if (in_array($context, $term_agencies)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove the default category metabox
     */
    public function remove_default_meta_box() {
        foreach ($this->object_types as $type) {
            remove_meta_box($this->name . 'div', $type, 'normal');
        }
    }

    /**
     * Adds the custom category metabox
     */
    public function add_custom_category_meta_box() {
        if ($this->context_has_terms()) {
            foreach ($this->object_types as $type) {
                add_meta_box($this->name, $this->args['labels']['name'], array($this, 'show_custom_category_meta_box'), $type, 'side');
            }
        }
    }

    /**
     * Show the custom category metabox. Which uses the Agency Terms Walker.
     */
    public function show_custom_category_meta_box($post) {
        echo '<div id="' . $this->name . '-all" class="tabs-panel categorydiv">';
            echo '<ul id="' . $this->name . 'checklist" class="list:' . $this->name . ' categorychecklist form-no-clear">';

                $args = array (
                    'taxonomy' => $this->name,
                    'walker'   => new \Walker_Agency_Terms,
                );

                wp_terms_checklist($post->ID, $args);

            echo '</ul>';
        echo '</div>';
    }
}
