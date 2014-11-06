<?php if (!defined('ABSPATH')) die();

/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
class Page_single_news extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/news_single/main', $this->get_data());
      get_footer();
    }
  }

  function get_data(){
    $article_date = get_the_date();

    return array(
      'id' => $post->ID,
      'thumbnail' => wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'newshead'),
      'thumbnail_caption' => get_post_thumbnail_caption(),
      'author' => get_the_author(),
      'title' => get_the_title(),
      'excerpt' => get_the_excerpt(),
      'content' => get_the_content(),
      'raw_date' => $article_date,
      'human_date' => date("j F Y", strtotime($article_date))
    );
  }
}

new Page_single_news();
