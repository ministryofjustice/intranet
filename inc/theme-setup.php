<?php
/**
 * Setup functions for the theme
 *
 */
add_action('after_setup_theme', 'create_pages');
function create_pages(){

        $theme_switcher_id = get_page_by_path( 'agency-switcher' );

        if (!$theme_switcher_id) {

            //create a new page and automatically assign the page template
            $post1 = array(
                'post_title' => "Agency Switcher",
                'post_content' => "",
                'post_status' => "publish",
                'post_type' => 'page',

            );

            $postID = wp_insert_post($post1);

            update_post_meta($postID, "_wp_page_template", "agency-switcher.php");

        }
}