<?php if (!defined('ABSPATH')) die();

/**
 * Template name: News landing
*/

class Page_news extends MVC_controller {
  function __construct() {
    parent::__construct();
    $this->page_number = get_query_var('paged') ?: 1;
  }

  function main() {
    get_header();
    $this->view('shared/breadcrumbs');
    $this->view('pages/news_landing/main', $this->get_data());
    get_footer();
  }

  function get_data() {
    $results = $this->get_news_from_API();

    return array(
      'results' => array()
    );
  }

  private function get_news_from_API() {
    $results = new news_request(array('', '', 1));
    return htmlspecialchars(json_encode($results->results_array));
  }
}

new Page_news();
