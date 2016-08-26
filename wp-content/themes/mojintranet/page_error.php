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
    $this->view('layouts/default', $this->get_data('404'));
  }

  function error500() {
    $this->view('layouts/default', $this->get_data('500'));
    trigger_error('Controller not found: ' . $controller_path . '. Entry point: ' . $_SERVER['REQUEST_URI'], E_USER_ERROR);
  }
  private function get_data($view) {
    return array(
      'page' => 'pages/error/' . $view,
      'template_class' => 'error',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */
    );
  }
}
