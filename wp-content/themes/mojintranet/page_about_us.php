<?php if (!defined('ABSPATH')) die();
/* Template name: About us */

class Page_about_us extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      $this->post_ID = get_the_ID();

      $this->view('layouts/default', $this->get_data());
    }
  }

  private function get_data() {
    $post = get_post($this->post_ID);

    return array(
      'page' => 'pages/about_us/main',
      'template_class' => 'about-us',
      'cache_timeout' => 60 * 15, /* 15 minutes */
      'page_data' => array(
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'children_data' => $this->get_children_data()
      )
    );
  }

  private function get_children_data() {
    $options = [
      'page_id' => $this->post_ID,
      'depth' => 2
    ];
    $response = $this->model->page_tree->get_children($options);
    $children = $response['children'];

    return $children;
  }
}
