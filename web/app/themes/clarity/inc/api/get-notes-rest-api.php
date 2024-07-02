<?php

/**
 *  Notes selective API
 *
 * @package Clarity
 */

use JetBrains\PhpStorm\NoReturn;

add_action('wp_ajax_get_notes_api', 'get_notes_api');
add_action('wp_ajax_nopriv_get_notes_api', 'get_notes_api');

// lazy load
add_action('wp_ajax_get_note_from_antonia', 'get_note_from_antonia');
add_action('wp_ajax_nopriv_get_note_from_antonia', 'get_note_from_antonia');

// $set_cpt custom post type
#[NoReturn] function get_note_from_antonia(): void
{
    define('NOTES_REST_REQUEST', true);
    $post_id = $_REQUEST['notes_id'] ?? 0;

    $post = get_post($post_id);

    include locate_template('src/components/c-notes-from-antonia/view.php');

    wp_die();
}

// $set_cpt custom post type
function get_notes_api($set_cpt = ''): void
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
