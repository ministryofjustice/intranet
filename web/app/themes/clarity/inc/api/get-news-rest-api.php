<?php

/**
 *  News feed API
 *
 *  @package Clarity
 */

use MOJ\Intranet\Agency;

add_action('wp_ajax_get_news_api', 'get_news_api');
add_action('wp_ajax_nopriv_get_news_api', 'get_news_api');

// $set_cpt custom post type
function get_news_api($set_cpt = '')
{
    $oAgency           = new Agency();
    $activeAgency      = $oAgency->getCurrentAgency();
    $post_per_page     = 'per_page=10';
    $current_page      = '&page=1';
    $agency_name       = '&agency=' . $activeAgency['wp_tag_id'];
    $post_type         = get_post_type();
    $post_id           = get_the_ID();
    $region_id         = get_the_terms($post_id, 'region');
    $regional_template = get_post_meta(get_the_ID(), 'dw_regional_template', true);

    // Internal http call used by the WordPress API
    $siteurl = 'http://' . $_SERVER['SERVER_NAME'];

    if ($region_id) :
        foreach ($region_id as $region) :
            // Current region, ie Scotland, North West etc
            $current_region = '&region=' . $region->term_id;
        endforeach;
    endif;

    switch ($post_type) {
        case 'regional_page':
            $response = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $set_cpt . '?' . 'per_page=4' . $current_page . $current_region);
            if ($regional_template === 'page_regional_archive_news.php') :
                $response = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $set_cpt . '?' . 'per_page=30' . $current_page . $current_region);
            endif;
            break;

        case is_singular('regional_news'):
            $response = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $set_cpt . '?' . 'per_page=6' . $current_page . $current_region);
            break;

        case is_singular('news'):
            $response = wp_remote_get($siteurl . '/wp-json/wp/v2/news/?' . 'per_page=6' . $current_page . $agency_name);
            break;

        default:
            $response = wp_remote_get($siteurl . '/wp-json/wp/v2/news/?' . $post_per_page . $current_page . $agency_name);
    }

    if (is_wp_error($response)) :
        return;
    endif;

    /*
    * Return API results and populate component(s)
    *
    */
    $pagetotal        = wp_remote_retrieve_header($response, 'x-wp-totalpages');
    $posts            = json_decode(wp_remote_retrieve_body($response), true);
    $response_code    = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (200 == $response_code && $response_message == 'OK') {
        if ($posts) {
            echo '<div class="data-type" data-type="' . $set_cpt . '"></div>';

             // We don't want the News title to appear in some sections.
            if (is_page_template('page_news.php') || is_single() || is_singular('regional_news')) :
                echo '';
            else :
                echo '<h2 class="o-title o-title--section" id="title-section">News</h2>';
            endif;

            foreach ($posts as $key => $post) {
                // This checks if the same post is appearing on the page twice and removes it.
                if ($post['id'] === $post_id) {
                    $post['id'] = '';
                } else {
                    include locate_template('src/components/c-article-item/view-news-feed.php');
                }
            }
        } else {
                echo '<!-- No posts available to return -->';
        }
    }// if (200 == $response_code && $response_message == 'OK'):
}
