<?php if (!defined('ABSPATH')) die();

/**
 * The default template
 */
class Page_default extends MVC_controller {
  function main() {
    while(have_posts()) {
      the_post();
      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data() {
    ob_start();
    the_content();
    $content = ob_get_clean();

    return array(
      'page' => 'pages/default/main',
      'page_data' => array(
        'title' => get_the_title(),
        'content' => $content
      )
    );
  }
}

new Page_default();
