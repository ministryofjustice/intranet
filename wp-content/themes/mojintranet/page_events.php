<?php if (!defined('ABSPATH')) die();

/**
 * Template name: Events landing
*/

class Page_events extends MVC_controller {
  private $post;

  function __construct() {
    $this->post = get_post($id);
    parent::__construct();
  }

  function main() {
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    $top_slug = $this->post->post_name;

    return array(
      'page' => 'pages/events_landing/main',
      'template_class' => 'events-landing',
      'breadcrumbs' => true,
      'cache_timeout' => 60 * 60 * 24, /* 1 day */
      'page_data' => array(
        'top_slug' => htmlspecialchars($top_slug),
        'tabs' => array(
          array(
            'label' => 'News',
            'url' => site_url('/newspage'),
            'selected' => false
          ),
          array(
            'label' => 'Events',
            'url' => site_url('/events'),
            'selected' => true
          )
        )
      )
    );
  }
}
