<?php

/**
 * Blogroll selective API
 *
 * @package Clarity
 */

add_action('wp_ajax_get_blogroll_posts_api', 'get_blogroll_posts_api');
add_action('wp_ajax_nopriv_get_blogroll_posts_api', 'get_blogroll_posts_api');

// lazy load
add_action('wp_ajax_get_blogroll_post', 'get_blogroll_post');
add_action('wp_ajax_nopriv_get_blogroll_post', 'get_blogroll_post');

// $set_cpt custom post type
function get_blogroll_post()
{
    $post_id = $_REQUEST['notes_id'] ?? 0;

    $post = get_post($post_id);

    include locate_template('src/components/c-notes-from-antonia/view.php');

    wp_die();
}

// $set_cpt custom post type
function get_blogroll_posts_api($set_cpt = '')
{
    $args = [
        'post_type' => $set_cpt,
        'numberposts' => -1
    ];

    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $key => $post) {
            include locate_template('src/components/c-article-item/view-notes-feed.php');
            if (($key !== 0) && $key % 3 == 0) {
                echo '<br><br><a href="#top">Back to top</a>';
            }
        }
    } else {
        echo '<!-- No notes available to return -->';
    }
}
