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
        
        $term_agencies = get_field('term_used_by', $category->taxonomy.'_'.$category->term_id);

        if(in_array($context, $term_agencies)) {
            
            if (empty($args['taxonomy'])) {
                $taxonomy = 'category';
            } else {
                $taxonomy = $args['taxonomy'];
            }

            if ($taxonomy == 'category') {
                $name = 'post_category';
            } else {
                $name = 'tax_input[' . $taxonomy . ']';
            }

            $args['popular_cats'] = empty($args['popular_cats']) ? array() : $args['popular_cats'];
            $class = in_array($category->term_id, $args['popular_cats']) ? ' class="popular-category"' : '';

            $args['selected_cats'] = empty($args['selected_cats']) ? array() : $args['selected_cats'];

            if (!empty($args['list_only'])) {
                $aria_cheched = 'false';
                $inner_class = 'category';

                if (in_array($category->term_id, $args['selected_cats'])) {
                    $inner_class .= ' selected';
                    $aria_cheched = 'true';
                }

                /** This filter is documented in wp-includes/category-template.php */
                $output .= "\n" . '<li' . $class . '>' .
                    '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                    ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                    esc_html(apply_filters('the_category', $category->name)) . '</div>';
            } else {
                /** This filter is documented in wp-includes/category-template.php */
                $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                    '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' .
                    checked(in_array($category->term_id, $args['selected_cats']), true, false) .
                    disabled(empty($args['disabled']), false, false) . ' /> ' .
                    esc_html(apply_filters('the_category', $category->name)) . '</label>';
            }
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

        $term_agencies = get_field('term_used_by', $category->taxonomy.'_'.$category->term_id);

        if(in_array($context, $term_agencies)) {
            $output .= "</li>\n";
        }
    }
}
