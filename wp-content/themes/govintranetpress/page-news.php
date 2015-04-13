<?php if (!defined('ABSPATH')) die();

/**
 * Template name: News landing
*/

class Page_news extends MVC_controller {
  private $post;

  function __construct() {
    $this->post = get_post($id);
    parent::__construct();
  }

  function main() {
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    $top_slug = $this->post->post_name;

    return array(
      'page' => 'pages/news_landing/main',
      'breadcrumbs' => true,
      'page_data' => array(
        'top_slug' => htmlspecialchars($top_slug)
      )
    );
  }
}

new Page_news();
