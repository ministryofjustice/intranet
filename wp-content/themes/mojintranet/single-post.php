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

    if (get_array_value($_GET, 'preview', 'false') == 'true') {
      $revisions = wp_get_post_revisions($post->ID);

      if (count($revisions) > 0) {
        $latest_revision = array_shift($revisions);
        $this_id = $latest_revision->ID;
      }
    }

    $thumbnail_id = get_post_thumbnail_id($this_id);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'intranet-large');
    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $authors = dw_get_author_info($this_id);

    $prev_post = get_previous_post();
    $next_post = get_next_post();

    $this->add_global_view_var('commenting_policy_url', site_url('/commenting-policy/'));
    $this->add_global_view_var('comments_open', (boolean) comments_open($this_id));
    $this->add_global_view_var('comments_on', (boolean) get_post_meta($this_id, 'dw_comments_on', true));
    $this->add_global_view_var('logout_url', wp_logout_url($_SERVER['REQUEST_URI']));

    $likes = $this->get_likes_from_api($this_id);

    return [
      'page' => 'pages/blog_post/main',
      'template_class' => 'blog-post',
      'page_data' => [
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
        'share_bar' => [
          'share_email_body' => "Hi there,\n\nI thought you might be interested in this page I've found on the MoJ intranet:\n",
          'likes_count' => $likes['count'],
          ]
      ]
    ];
  }

  private function get_likes_from_api($post_id) {
    return $this->model->likes->read('post', $post_id);
  }
}
