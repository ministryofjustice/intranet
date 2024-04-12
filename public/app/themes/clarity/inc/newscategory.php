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

        $post_per_page = 10;

        $args = [
            'numberposts' => $post_per_page,
            'post_type' => 'news',
            'post_status' => 'publish',
            'tax_query' => [
              'relation' => 'AND',
              [
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $activeAgency['wp_tag_id']
              ],
              ...( $category_id ? [
                'taxonomy' => 'news_category',
                'field' => 'category_id',
                'terms' =>  $category_id,
              ] : []),
          ]
        ];

        $posts = get_posts($args);

        foreach ($posts as $key => $post) {
            $news_post_id = $post->ID;
            $link = get_the_permalink($post->ID);
            $author = $post->post_author;
            $news_link        = $link;
            $author_image     = $author ? get_the_author_meta('display_name', $author) : '';
            $author_name      = $author ? get_the_author_meta('thumbnail_avatar', $author) : '';
            $news_title       = $post->post_title;
            $news_date        = $post->post_date;
            $news_excerpt     = $post->post_excerpt;
            $featured_img_url = wp_get_attachment_url(get_post_thumbnail_id($news_post_id));

            include(locate_template('src/components/c-article-item/view-newscategory.php'));
        }
    }
}
