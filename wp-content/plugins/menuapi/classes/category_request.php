<?php

/**
 * Processes API requests for subcategories of category
 *
 * @author ryanajarrett
 */
class category_request {

    private $results_array = array();

    function __construct($pageid = 0) {
        // Get page details
        $submenu_page = new WP_Query(array(
            'p' => $pageid,
            'post_type' => array('page')
        ));
        $submenu_page->the_post();

        // Start JSON
        // Category name
        $this->results_array['title'] = get_the_title();
        // Subcategories Start
        $subcats = new WP_Query(array(
            'post_parent' => $pageid,
            'post_type' => array('page')
        ));
        if ($subcats->have_posts()) {
            while ($subcats->have_posts()) {
                $subcats->the_post();
                $this->results_array['items'][] = $this->build_subcat(get_the_ID());
            }
        } // Subcategories End
        // End JSON
        $this->output_json();
    }

    function build_subcat($subcat_id = 0) {
        $subcat_page = new WP_Query(array(
            'p' => $subcat_id,
            'post_type' => array('page')
        ));
        $subcat_page->the_post();
        // Subcategory Start
        $subcat_array = array();
        // Subcategory ID
        $subcat_array['id'] = $subcat_id;
        // Subcategory Title
        $subcat_array['title'] = get_the_title();
        // Subcategory URL
        $subcat_array['url'] = get_the_permalink();
        // Subcategory Excerpt
        $subcat_array['excerpt'] = get_the_excerpt();
        // Subcategory Order
        $subcat_array['order'] = $subcat_page->menu_order;
        // Subcategory End
        return $subcat_array;
    }

    function output_json() {
        echo json_encode($this->results_array);
    }

}
