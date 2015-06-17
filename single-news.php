<?php if (!defined('ABSPATH')) die();

class Single_news extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    $article_date = get_the_date();

    ob_start();
    the_content();
    $content = ob_get_clean();

    $this_id = $post->ID;

    $thumbnail_id = get_post_thumbnail_id($this_id);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'newshead');
    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

    $prev_news = get_previous_post();
    $next_news = get_next_post();

    return array(
      'page' => 'pages/news_single/main',
      'template_class' => 'single-news',
      'page_data' => array(
        'id' => $this_id,
        'thumbnail' => $thumbnail[0],
        'thumbnail_alt_text' => $alt_text,
        'thumbnail_caption' => get_post_thumbnail_caption(),
        'author' => get_the_author(),
        'title' => get_the_title(),
        'excerpt' => get_the_excerpt(),
        'content' => $content,
        'raw_date' => $article_date,
        'human_date' => date("j F Y", strtotime($article_date)),
        'prev_news_exists' => is_object($prev_news),
        'next_news_exists' => is_object($next_news),
        'prev_news_url' => get_post_permalink($prev_news),
        'next_news_url' => get_post_permalink($next_news),
        'election_banner' => array(
          'visible' => strtotime($article_date) < strtotime('9 May 2015')?1:0
        )
      )
    );
  }
}
