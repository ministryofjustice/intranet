<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}
/*
*
* This class generates news posts through newscategory via the WP API
*
*/

class NewsCategory
{
    public function get_newscategory_list($category_id)
    {
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $post_per_page      = 'per_page=10';
        $current_page       = '&page=1';
        $agency_name        = '&agency=' . $activeAgency['wp_tag_id'];
        $category_name      = '&news_category=' .$category_id;

        /*
        * A temporary measure so that API calls do not get blocked by
        * changing IPs not whitelisted. All calls are within container.
        */
        $siteurl = 'http://127.0.0.1';

        $response = wp_remote_get($siteurl.'/wp-json/wp/v2/news/?' . $post_per_page . $current_page . $agency_name . $category_name);

        if (is_wp_error($response)) {
            return;
        }

        $pagetotal = wp_remote_retrieve_header($response, 'x-wp-totalpages');

        $posts = json_decode(wp_remote_retrieve_body($response), true);

        $response_code       = wp_remote_retrieve_response_code($response);
        $response_message = wp_remote_retrieve_response_message($response);

        if (200 == $response_code && $response_message == 'OK') {
            if (is_array($posts)) {
                foreach ($posts as $key => $post) {
                    $news_post_id     = $post['id'];
                    $news_link        = $post['link'];
                    $author_image     = isset($post['coauthors'][0]['thumbnail_avatar']);
                    $author_name      = isset($post['coauthors'][0]['display_name']);
                    $news_title       = $post['title']['rendered'];
                    $news_date        = $post['date'];
                    $news_excerpt     = $post['excerpt']['rendered'];
                    $featured_img_url = wp_get_attachment_url(get_post_thumbnail_id($news_post_id));

                    include(locate_template('src/components/c-article-item/view-newscategory.php'));
                }
            }
        }
    }
}
