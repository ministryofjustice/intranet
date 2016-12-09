<?php if (!defined('ABSPATH')) die();

/**
 * Template name: Events landing
*/

class Page_events extends MVC_controller {
  private $post;

  function main() {
    $this->post = get_post();
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    return [
      'page' => 'pages/events_landing/main',
      'template_class' => 'events-landing',
      'breadcrumbs' => true,
      'cache_timeout' => 60 * 60 * 24, /* 1 day */
      'page_data' => [
        'page_base_url' => rtrim(get_permalink($this->post_id), '/'),
      ]
    ];
  }
}
