<?php

/**
 * The template for displaying Search Results pages.
 *
 * Template name: Search results
 */

class Page_search_results extends MVC_controller {
  private $post;

  function __construct() {
    $this->post = get_post($id);
    parent::__construct();
  }

  function main() {
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    $top_slug = $this->post->post_name;

    return array(
      'page' => 'pages/search_results/main',
      'template_class' => 'search-results',
      'page_data' => array(
        'top_slug' => htmlspecialchars($top_slug)
      )
    );
  }
}
