<?php

/**
 *  Notes selective API
 *
 * @package Clarity
 */

add_action('wp_ajax_get_notes_api', 'get_notes_api');
add_action('wp_ajax_nopriv_get_notes_api', 'get_notes_api');

// lazy load
add_action('wp_ajax_get_note_from_antonia', 'get_note_from_antonia');
add_action('wp_ajax_nopriv_get_note_from_antonia', 'get_note_from_antonia');

// $set_cpt custom post type
function get_note_from_antonia()
{
    $post_id = $_REQUEST['notes_id'] ?? 0;

    $post = get_post($post_id);

    include locate_template('src/components/c-notes-from-antonia/view.php');

    wp_die();
}

// $set_cpt custom post type
function get_notes_api($set_cpt = '')
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
                echo '<a class="c-notes-from-antonia__to-the-top" href="#top">Back to top</a>';
            }
        }
    } else {
        echo '<!-- No notes available to return -->';
    }
}
