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
    return [
      'page' => 'pages/homepage/main',
      'template_class' => 'home',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */,
      'no_breadcrumbs' => true,
      'page_data' => [
        'news_widget' => [
          'see_all_url' => get_permalink(Taggr::get_id('news-landing')),
          'see_all_label' => 'See all news',
          'type' => 'global',
          'number_of_lists' => 2,
          'no_items_found_message' => 'No news found',
          'list_container_classes' => 'col-lg-6 col-md-12 col-sm-12',
          'skeleton_screen_count' => 4,
          'heading_text' => 'News'
        ],
        'events_widget' => [
          'see_all_url' => get_permalink(Taggr::get_id('events-landing')),
          'see_all_label' => 'See all events',
          'no_items_found_message' => 'No events found',
          'type' => 'global'
        ],
        'posts_widget' => [
          'see_all_url' => get_permalink(Taggr::get_id('blog-landing')),
          'see_all_label' => 'See all posts',
          'no_items_found_message' => 'No posts found',
          'type' => 'global',
          'number_of_lists' => 1,
          'list_container_classes' => 'col-lg-12 col-md-12 col-sm-12',
          'skeleton_screen_count' => 5
        ]
      ]
    ];
  }
}
