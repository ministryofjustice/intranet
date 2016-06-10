<?php

// Include Walker_Category_Checklist class
require_once( ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php' );

class Walker_Agency_Terms extends Walker_Category_Checklist {
    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 2.5.1
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category The current term object.
     * @param int    $depth    Depth of the term in reference to parents. Default 0.
     * @param array  $args     An array of arguments. @see wp_terms_checklist()
     * @param int    $id       ID of the current term.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        $context = Agency_Context::get_agency_context('term_id');
        $term_agencies = get_field('term_used_by', $category->taxonomy . '_' . $category->term_id);

        if(in_array($context, $term_agencies)) {
            parent::start_el($output, $category, $depth, $args, $id);
        }
    }

    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @since 2.5.1
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category The current term object.
     * @param int    $depth    Depth of the term in reference to parents. Default 0.
     * @param array  $args     An array of arguments. @see wp_terms_checklist()
     */
    public function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $context = Agency_Context::get_agency_context('term_id');
        $term_agencies = get_field('term_used_by', $category->taxonomy . '_' . $category->term_id);

        if(in_array($context, $term_agencies)) {
            parent::end_el($output, $category, $depth, $args);
        }
    }
}
