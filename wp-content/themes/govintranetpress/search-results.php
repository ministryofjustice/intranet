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
    get_header();
    $this->view('shared/breadcrumbs');
    $this->view('pages/search_results/main', $this->get_data());
    get_footer();
  }

  private function get_data() {
    $top_slug = $this->post->post_name;

    return array(
      'top_slug' => htmlspecialchars($top_slug)
    );
  }
}

new Page_search_results();
