<?php if (!defined('ABSPATH')) die();

class Emergency_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get_emergency_banner($options = array()) {
    $data['visible'] = (int) get_option("emergency_toggle");
    $data['title'] = get_option("emergency_title");
    $data['date'] = get_option("emergency_date");
    $message = get_option("homepage_control_emergency_message");
    $data['message'] = apply_filters('the_content', $message, true);
    $data['type'] = get_option("emergency_type");

    return $data;
  }
}
