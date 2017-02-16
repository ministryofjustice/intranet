<?php if (!defined('ABSPATH')) die();

class Emergency_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get($options = []) {
    $data = [];
    $agency = get_array_value($options, 'agency', 'hq');
    $message = get_option($agency . '_homepage_control_emergency_message');
    $type = get_option($agency . '_emergency_type');

    $data['title'] = get_option($agency . '_emergency_title');
    $data['date'] = get_option($agency . '_emergency_date');
    $data['message'] = apply_filters('the_content', $message, true);
    $data['type'] = !$type ? 'emergency' : $type;

    return $data;
  }
}
