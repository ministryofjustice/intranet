<?php if (!defined('ABSPATH')) die();

/**
 * Template name: News landing
*/

class Page_news extends MVC_controller {
  private $page_number;
  private $post;

  function __construct() {
    $this->page_number = get_query_var('paged') ?: 1;
    $this->post = get_post(get_the_id());

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

  private function get_news_from_API() {
    $results = new news_request(array('', '', 1));
    return htmlspecialchars(json_encode($results->results_array));
  }
}

new Page_news();
