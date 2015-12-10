<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * Template name: Home page
 */

class Page_error extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    return array(
      'page' => 'pages/error/main',
      'template_class' => 'error',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */
    );
  }
}
