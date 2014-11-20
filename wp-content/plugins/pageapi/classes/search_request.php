<?php

/**
 * Processes API requests for the flexible A-Z/index page
 *
 * @author ryanajarrett
 * @since 0.2
 */
class search_request extends api_request {

	public static $params = array('type','category','keywords','initial','page','per_page');

	function __construct() {
        // Setup vars from url params
        $this->set_params();
        // Check search type - if not page or doc, default to page
        if(!in_array($this->data['type'],array("page","doc"),true)) {
            $this->data['type'] = "page";
        }

        // If initial set, limit WP_Query args to matching post IDs
        if (strlen($this->data['initial'])===1) {
            global $wpdb;
            $postids=$wpdb->get_col($wpdb->prepare("
                SELECT      ID
                FROM        $wpdb->posts
                WHERE       SUBSTR($wpdb->posts.post_title,1,1) = %s
                ORDER BY    $wpdb->posts.post_title",$this->data['initial'])
            );
        } else {
            $postids = null;
        }

        // Set paging options
        $nopaging = true;
        if(is_numeric($this->data['page'])) {
            $paged = $this->data['page'];
            $nopaging = false;
        } else {
            $paged = null;
        }
        if(is_numeric($this->data['per_page'])) {
            $per_page =  $this->data['per_page'];
            $nopaging = false;
        } else {
            $per_page = 10;
        }

        // Set up WP_Query params
        $args = array(
            // Paging
            'nopaging'          =>  $nopaging,
            'paged'             =>  $paged,
            'posts_per_page'    =>  $per_page,
            // Sorting
            'order'             => 'ASC',
            'orderby'           => 'title',
            // Filters
            'post_type'         =>  $this->data['type'],
            'category_name'     =>  $this->data['category'],
            's'                 =>  $this->data['keywords'],
            // Restricts posts for first letter
            'post__in'          =>  $postids
        );

        // Get matching results
        $results = new WP_Query($args);

        if($results->have_posts()) {

            // Start JSON
            // Loop through alphabet
            $result_letters = range('A', 'Z');
            global $post;

            // Get first letter
            $result_letter = current($result_letters);
            $letter_array = array();
            $letter_array['initial'] = $result_letter;
            $letter_array['results'] = array();
            // Get first post
            $results->the_post();
            $last_post = false;
            do {
                if($result_letter==strtoupper(substr(get_the_title(),0,1)) && !$last_post) {
                    $letter_array['results'][] = array(
                        'title' =>  get_the_title(),
                        'url'   =>  get_the_permalink(),
                        'slug'  =>  $post->post_name,
                        'excerpt'   =>  get_the_excerpt()
                    );
                    if($results->current_post+1 != $results->post_count) {
                        $results->the_post();
                    } else {
                        $last_post = true;
                    }
                } else {
                    // Store current result
                    $this->results_array[] = $letter_array;
                    // Get next letter
                    $result_letter = next($result_letters);
                    // Set up new result array
                    $letter_array = array(
                        'initial' => $result_letter,
                        'results' => array()
                    );
                }
            } while ($result_letter!=="Z" || ($results->current_post+1 != $results->post_count));
            // Store final result
            $this->results_array[] = $letter_array;
        }

        return($this->results_array);
	}
}
?>