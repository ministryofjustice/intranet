<?php if (!defined('ABSPATH')) die();

class Single_post extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    global $post;

    $article_date = get_the_date();

    ob_start();
    the_content();
    $content = ob_get_clean();

    $this_id = $post->ID;

    $thumbnail_id = get_post_thumbnail_id($this_id);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'intranet-large');
    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $authors = dw_get_author_info($post->ID);
    $likes = $this->get_likes_from_api($this_id);

    $prev_post = get_previous_post();
    $next_post = get_next_post();

    return array(
      'page' => 'pages/blog_post/main',
      'template_class' => 'blog-post',
      'cache_timeout' => 60 * 5, /* 5 minutes */
      'page_data' => array(
        'id' => $this_id,
        'thumbnail' => $thumbnail[0],
        'thumbnail_alt_text' => $alt_text,
        'thumbnail_caption' => get_post_thumbnail_caption(),
        'author' => $authors[0]['name'],
        'author_thumbnail_url' => $authors[0]['thumbnail_url'],
        'job_title' => $authors[0]['job_title'],
        'title' => get_the_title(),
        'excerpt' => get_the_excerpt(),
        'content' => $content,
        'raw_date' => $article_date,
        'human_date' => date("j F Y", strtotime($article_date)),
        'prev_post_exists' => is_object($prev_post),
        'next_post_exists' => is_object($next_post),
        'prev_post_url' => get_post_permalink($prev_post),
        'next_post_url' => get_post_permalink($next_post),
        'share_email_body' => "Hi there,\n\nI thought you might be interested in this blog post I've found on the MoJ intranet:\n",
        'likes_count' => $likes['count'],
        'like_nonce' => wp_create_nonce('dw_inc_likes'),
        'comment_nonce' => wp_create_nonce('dw_comment')
      )
    );
  }

  private function get_likes_from_api($post_id) {
    $results = new likes_request(array($post_id));
    return $results->results_array;
  }
}
