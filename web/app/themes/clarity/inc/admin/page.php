<?php

// Page post type. Add excerpts to pages
add_action('init', 'add_page_excerpts');

function add_page_excerpts()
{
    add_post_type_support('page', 'excerpt');
}

// Ensure that the template for the agency-switcher page is persisted.
add_action('save_post', function ($post_id, $post) {
    if ($post->post_name === 'agency-switcher') {
        update_post_meta($post_id, '_wp_page_template', 'agency-switcher.php');
    }
}, 99, 2);