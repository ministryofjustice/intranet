<?php

/**
 * The template for displaying Search Results pages.
 *
 * Template name: Home page
 */
class Page_home extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    return array(
      'page' => 'pages/homepage/main',
      'template_class' => 'home',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'emergency_message' => $this->get_emergency_message(),
        'my_moj' => $this->model->my_moj->get_data(),
        'events' => $this->get_events(),
        'posts' => $this->get_posts(),
        'see_all_events_url' => get_permalink(Taggr::get_id('events-landing')),
        'see_all_posts_url' => get_permalink(Taggr::get_id('blog-landing'))
      )
    );
  }

  private function get_emergency_message() {
    $visible = get_option("emergency_toggle");
    $title = get_option("emergency_title");
    $date = get_option("emergency_date");
    $message = get_option("homepage_control_emergency_message");
    $message = apply_filters('the_content', $message, true);
    $type = get_option("emergency_type");

    return array(
      'visible'     => $visible,
      'title'       => $title,
      'date'        => $date,
      'message'     => $message,
      'type'        => $type
    );
  }

  private function get_events() {
    $events = $this->get_events_from_api();
    $formatted_events = array();

    if($events['results']) {
      foreach($events['results'] as $event) {
        $start_date_timestamp = strtotime($event['start_date']);
        $end_date_timestamp = strtotime($event['end_date']);

        $formatted_events[] = array(
          'url' => $event['url'],
          'title' => $event['title'],
          'human_date' => date("j F Y", $start_date_timestamp),
          'day_of_week' => date("l", $start_date_timestamp),
          'day_of_month' => date("j", $start_date_timestamp),
          'month_year' => date("M Y", $start_date_timestamp),
          'date' => date("j F Y", $start_date_timestamp) . ' - ' . date("j F Y", $end_date_timestamp),
          'time' => $event['all_day'] ? 'All day' : $event['start_time'] . ' - ' . $event['end_time'],
          'multiday' => $event['multiday'],
          'all_day' => $event['all_day'],
          'location' => $event['location']
        );
      }
    }

    return $formatted_events;
  }

  private function get_posts() {
    $posts = $this->get_posts_from_api();

    $formatted_posts = array();

    foreach($posts['results'] as $post) {
      $post['human_date'] = date("j M Y", strtotime($post['timestamp']));
      $post['avatar'] = $post['authors'][0]['thumbnail_url'];

      $formatted_posts[] = $post;
    }

    return $formatted_posts;
  }

  private function get_events_from_api() {
    return $this->model->events->get_list(array(
      'per_page' => 3
    ));
  }

  private function get_posts_from_api() {
    return $this->model->post->get_list(array(
      'per_page' => 2
    ));
  }
}
