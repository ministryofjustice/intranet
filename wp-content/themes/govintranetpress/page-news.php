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
    get_header();
    $this->view('shared/breadcrumbs');
    $this->view('pages/news_landing/main', $this->get_data());
    get_footer();
  }

  function get_data() {
    $top_slug = $this->post->post_name;

    return array(
      'top_slug' => htmlspecialchars($top_slug)
    );
  }
}

new Page_news();
