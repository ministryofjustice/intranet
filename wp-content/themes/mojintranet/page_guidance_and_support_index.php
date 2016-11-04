<?php if (!defined('ABSPATH')) die();
/* Template name: Guidance & Support Index */

class Page_guidance_and_support_index extends MVC_controller {
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
      'page' => 'pages/guidance_and_support/main',
      'template_class' => 'guidance-and-support-index',
      'cache_timeout' => 60 * 30, /* 30 minutes */
      'page_data' => array(
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
      )
    );
  }
}
