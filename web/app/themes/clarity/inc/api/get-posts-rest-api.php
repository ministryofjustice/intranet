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

    $post_id      = get_the_ID();
    $oAgency      = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $post_per_page = 'per_page=' . $blog_posts_number;
    $current_page  = '&page=1';
    $agency_name   = '&agency=' . $activeAgency['wp_tag_id'];

    /*
    * A temporary measure so that API calls do not get blocked by
    * changing IPs not whitelisted. All calls are within container.
    */
    $siteurl = 'http://127.0.0.1';

    $response = wp_remote_get($siteurl . '/wp-json/wp/v2/posts/?' . $post_per_page . $current_page . $agency_name);

    if (is_wp_error($response)) {
        return;
    }

    $pagetotal = wp_remote_retrieve_header($response, 'x-wp-totalpages');

    $posts = json_decode(wp_remote_retrieve_body($response), true);

    $response_code    = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (200 == $response_code && $response_message == 'OK') {
        echo '<div class="data-type" data-type="posts"></div>';

        if (is_array($posts)) {
            foreach ($posts as $key => $post) {
                include locate_template('src/components/c-article-item/view-blog-feed.php');
            }
        }
    }
}
