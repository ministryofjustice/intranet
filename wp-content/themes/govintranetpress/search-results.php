<?php

/**
 * The template for displaying Search Results pages.
 *
 * Template name: Search results
 */

class Page_search_results extends MVC_controller {
  function main() {
    get_header();
    $this->view('shared/breadcrumbs');
    $this->view('pages/search_results/main', $this->get_data());
    get_footer();
  }

  private function get_data() {
    $data = array(
    );

    return $data;
  }
}

new Page_search_results();
