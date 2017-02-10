<?php if (!defined('ABSPATH')) die();

class Campaign_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get($options = []) {
    $data = [];
    $agency = get_array_value($options, 'agency', 'hq');
    $message = get_option($agency . '_homepage_control_emergency_message');
    $type = get_option($agency . '_emergency_type');

    return $data;
  }
}
