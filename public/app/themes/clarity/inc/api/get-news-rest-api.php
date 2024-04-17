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
    $post_per_page     = 10;
    $post_type         = 'news';
    $post_id           = get_the_ID();
    $region_id         = get_the_terms($post_id, 'region');
    $regional_template = get_post_meta(get_the_ID(), 'dw_regional_template', true);
    $regional          = false;

    $args = [];

    switch ($post_type) {
        case 'regional_page':
            $args['numberposts'] = 4;
            $args['post_type'] = $set_cpt;
            $regional = true;
            if ($regional_template === 'page_regional_archive_news.php') {
                $args['numberposts'] = 30;
            }
            break;

        case is_singular('regional_news'):
        case is_singular('news'):    
            $args['numberposts'] = 6;
            break;

        default:
            $args['numberposts'] = $post_per_page;
            $args['post_type'] = 'news';
    }

    $args['tax_query'] = [
        'relation' => 'AND',
        [
          'taxonomy' => 'agency',
          'field' => 'term_id',
          'terms' => $activeAgency['wp_tag_id']
        ],
        // If the region is set add its ID to the taxonomy query
        ...($regional ? [
          'taxonomy' => 'region',
          'field' => 'region_id',
          'terms' => $region_id,
        ] : []),
    ];

    $args['post_status'] = 'publish';

    $posts = get_posts($args);

    if ($posts) {
        echo '<div class="data-type" data-type="' . $set_cpt . '"></div>';
            // We don't want the News title to appear in some sections.
        if (is_page_template('page_news.php') || is_single() || is_singular('regional_news')) :
            echo '';
        else :
            echo '<h2 class="o-title o-title--section" id="title-section">News</h2>';
        endif;

        foreach ($posts as $key => $post) {
          include locate_template('src/components/c-article-item/view-news-feed.php');   
        }
    } else {
            echo '<!-- No posts available to return -->';
    }
}