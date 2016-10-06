<?php if (!defined('ABSPATH')) die();

class Single_event extends MVC_controller {
  function main() {
    while(have_posts()) {
      the_post();
      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    global $post;

    ob_start();
    the_content();
    $content = ob_get_clean();

    $this_id = $post->ID;
    $start_date = get_post_meta($post->ID, '_event-start-date', true);
    $end_date = get_post_meta($post->ID, '_event-end-date', true);
    $start_time = get_post_meta($post->ID, '_event-start-time', true);
    $end_time = get_post_meta($post->ID, '_event-end-time', true);
    $all_day = get_post_meta($post->ID, '_event-allday', true);

    $start_date_timestamp = strtotime($start_date);
    $end_date_timestamp = strtotime($end_date);
    $multiday = $start_date_timestamp != $end_date_timestamp;

    return array(
      'page' => 'pages/event_single/main',
      'template_class' => 'single-event',
      'cache_timeout' => 60 * 30, /* 30 minutes */
      'page_data' => array(
        'id' => $this_id,
        'author' => get_the_author(),
        'title' => get_the_title(),
        'content' => $content,
        'human_date' => date("j F Y", $start_date_timestamp),
        'day_of_week' => date("l", $start_date_timestamp),
        'day_of_month' => date("j", $start_date_timestamp),
        'month_year' => date("M Y", $start_date_timestamp),
        'date' => date("j F Y", $start_date_timestamp) . ' - ' . date("j F Y", $end_date_timestamp),
        'time' => $all_day == true ? 'All day' : $start_time . ' - ' . $end_time,
        'multiday' => $multiday,
        'all_day' => $all_day,
        'location' => get_post_meta($post->ID, '_event-location', true),
        'share_email_body' => "Hi there,\n\nI thought you might be interested in this event I've found on the MoJ intranet:\n"
      )
    );
  }
}
