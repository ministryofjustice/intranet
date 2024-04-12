<?php

/**
 *  Blog feed API
 *
 *  @package Clarity
 */

use MOJ\Intranet\Agency;

add_action('wp_ajax_get_post_api', 'get_post_api');
add_action('wp_ajax_nopriv_get_post_api', 'get_post_api');

function get_post_api($blog_posts_number = '10')
{

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $args = [
        'numberposts' => $blog_posts_number,
        'post_type' => 'post',
        'post_status' => 'publish',
        'tax_query' => [
          'relation' => 'AND',
          [
            'taxonomy' => 'agency',
            'field' => 'term_id',
            'terms' => $activeAgency['wp_tag_id']
          ],
      ]
    ];

    $posts = get_posts($args);

    echo '<div class="data-type" data-type="posts"></div>';
    foreach ($posts as $key => $post) {
        include locate_template('src/components/c-article-item/view-blog-feed.php');
    }

}
