<?php if (!defined('ABSPATH')) die();

class Months_model extends MVC_model {
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
    $data = $this->count_events($data);

    return $data;
  }

  private function count_events($data) {
    $data['results'] = array();

    for($x = 0; $x < 12; $x++) {
      $data['results'][date('Y-m-01', strtotime("+$x month"))] = 0;
    }

    foreach($data['raw']->posts as $post) {
      $start_date = get_post_meta($post->ID, '_event-start-date', true);
      $data['results'][date('Y-m-01', strtotime($start_date))]++;
    }

    unset($data['raw']);
    unset($data['retrieved_results']);

    return $data;
  }
}
