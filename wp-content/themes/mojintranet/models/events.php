<?php if (!defined('ABSPATH')) die();

include_once(dirname(__FILE__) . '/search.php');

class Events_model extends MVC_model {
  public function get_list($options = array()) {
    $options['search_orderby'] = array(
      '_event-start-date' => 'ASC',
      '_event-end-date' => 'ASC',
      'title' => 'ASC'
    );
    $options['meta_fields'] = array('_event-start-date', '_event-end-date');
    $options['post_type'] = 'event';

    $this->model->search->get($options);
  }
}
