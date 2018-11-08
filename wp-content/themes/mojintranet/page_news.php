<?php if (!defined('ABSPATH')) die();

/**
 * Template name: News landing
*/

class Page_news extends MVC_controller {
  private $post;

  function __construct($param_string, $post_id) {
    parent::__construct($param_string, $post_id);

    $this->model('taxonomy');
  }

  function main() {
    $this->post = get_post();
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    return [
      'page' => 'pages/news_landing/main',
      'template_class' => 'news-landing',
      'breadcrumbs' => true,
      'page_data' => [
        'page_base_url' => rtrim(get_permalink($this->post_id), '/'),
        'news_categories' => htmlspecialchars(json_encode($this->model->taxonomy->get([
          'taxonomy' => 'news_category'
        ])))
      ]
    ];
  }
}
