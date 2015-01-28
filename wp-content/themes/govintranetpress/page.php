<?php if (!defined('ABSPATH')) die();

/**
 * The default template
 */
class Page_default extends MVC_controller {
  function main() {
    while(have_posts()) {
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/default/main', $this->get_data());
      get_footer();
    }
  }

  function get_data() {
    ob_start();
    the_content();
    $content = ob_get_clean();

    return array(
      'title' => get_the_title(),
      'content' => $content
    );
  }
}

new Page_default();
