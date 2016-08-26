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
    $this->view('layouts/default', $this->get_data('main'));
  }

  function error404() {
    $this->view('layouts/default', $this->get_data('error404'));
  }

  function error500() {
    $this->view('layouts/default', $this->get_data('error500'));
  }
  private function get_data($view) {
    return array(
      'page' => 'pages/error/' . $view,
      'template_class' => 'error',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */
    );
  }
}
