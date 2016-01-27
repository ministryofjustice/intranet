<?php if (!defined('ABSPATH')) die();

class Events_model extends MVC_model {
  public function get_list($options = array()) {
    $options['search_orderby'] = array(
      '_event-start-date' => 'ASC',
      '_event-end-date' => 'ASC',
      'title' => 'ASC'
    );
    $options['meta_fields'] = array('_event-start-date', '_event-end-date');
    $options['post_type'] = 'event';
    $options['date'] = array(date('Y-m-01'), '11');

    $data = $this->model->search->get_raw($options);
    $data = $this->format_data($data);

    return $data;
  }

  private function format_data($data) {
    $data['results'] = array();

    foreach($data['raw']->posts as $post) {
      $data['results'][] = $this->format_row($post);
    }

    unset($data['raw']);

    return $data;
  }

  private function format_row($post) {
    $id = $post->ID;

    $start_date = get_post_meta($id, '_event-start-date', true);
    $end_date = get_post_meta($id, '_event-end-date', true);

    return array(
      'id' => $id,
      'title' => (string) get_the_title($id),
      'url' => (string) get_the_permalink($id),
      'slug' => (string) $post->post_name,
      'location' => (string) get_post_meta($id, '_event-location', true),
      'description' => (string) get_the_content_by_id($id),
      'start_date' => (string) $start_date,
      'start_time' => (string) get_post_meta($id, '_event-start-time', true),
      'end_date' => (string) $end_date,
      'end_time' => (string) get_post_meta($id, '_event-end-time', true),
      'all_day' => (string) get_post_meta($id, '_event-allday', true) == 'allday',
      'multiday' => (string) $start_date !== $end_date
    );
  }
}
