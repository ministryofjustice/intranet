<?php if (!defined('ABSPATH')) die();

class Campaign_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get($options = []) {
    $data = [];
    $agency = get_array_value($options, 'agency', 'hq');
    $message = get_option($agency . '_banner_image');
    $type = get_option($agency . '_banner_link');

    return $data;
  }
}
