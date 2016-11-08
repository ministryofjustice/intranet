<?php

/**
 * Template name: Home page
 */
class Page_home extends MVC_controller {
  function __construct($param_string, $post_id) {
    parent::__construct($param_string, $post_id);

    $this->model('follow_us');
  }

  function main() {
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    return array(
      'page' => 'pages/homepage/main',
      'template_class' => 'home',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'see_all_events_url' => get_permalink(Taggr::get_id('events-landing')),
        'see_all_posts_url' => get_permalink(Taggr::get_id('blog-landing'))
      )
    );
  }
}
