<?php if (!defined('ABSPATH')) die();

class Emergency_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get_emergency_banner($options = array()) {
    $agency = 'hq';

    if (array_key_exists('agency', $options) && strlen($options['agency']) > 0) {
      $agency = $options['agency'];
    }

    $data['visible'] = (int) get_option($agency . "_emergency_toggle");
    $data['title'] = get_option($agency . "_emergency_title");
    $data['date'] = get_option($agency . "_emergency_date");
    $message = get_option($agency . "_homepage_control_emergency_message");
    $data['message'] = apply_filters('the_content', $message, true);
    $type = get_option($agency . "_emergency_type");
    $data['type'] = !$type ? 'emergency' : $type;
    return $data;
  }
}
