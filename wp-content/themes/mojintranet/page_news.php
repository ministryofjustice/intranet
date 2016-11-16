<?php if (!defined('ABSPATH')) die();

/**
 * Template name: News landing
*/

class Page_news extends MVC_controller {
  private $post;

  function __construct($param_string, $post_id) {
    parent::__construct($param_string, $post_id);

    $this->model('taxonomy');
    $this->post = get_post();
  }

  function main() {
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    $top_slug = $this->post->post_name;

    return array(
      'page' => 'pages/news_landing/main',
      'template_class' => 'news-landing',
      'breadcrumbs' => true,
      'cache_timeout' => 60 * 60 * 24, /* 1 day */
      'page_data' => array(
        'top_slug' => htmlspecialchars($top_slug),
        'news_categories' => htmlspecialchars(json_encode($this->model->taxonomy->get([
          'taxonomy' => 'news_category'
        ])))
      )
    );
  }
}
