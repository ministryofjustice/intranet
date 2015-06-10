<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * Template name: Home page
 */

class Error extends MVC_controller {
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
      'template_class' => 'error'
    );
  }
}
