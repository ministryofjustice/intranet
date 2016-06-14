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
        'event_months' => $this->get_months()
      )
    );
  }

  private function get_months() {
    $months = $this->get_months_from_api();

    $formatted_months = array();

    foreach($months['results'] as $date=>$count) {
      $time = strtotime($date);
      $formatted_date = date("F Y", $time);
      $formatted_count = $count . ' ' . ($count===1 ? 'event' : 'events');
      $formatted_months[] = array(
        'label' => $formatted_date . '&nbsp;&nbsp;(' . $formatted_count . ')',
        'value' => date("Y-m", $time)
      );
    }

    return $formatted_months;
  }

  private function get_months_from_api() {
    return $this->model->months->get_list(array(
      'tax_query' => array(
        array(
          'taxonomy' => 'agency',
          'field'    => 'slug',
          'terms'    => 'hq' //!!! hard-coded for now
        )
      )
    ));
  }
}
