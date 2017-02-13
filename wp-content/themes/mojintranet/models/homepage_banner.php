<?php if (!defined('ABSPATH')) die();

class Homepage_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get($options = []) {
    $data = [];
    $agency = get_array_value($options, 'agency', 'hq');
    $banner_image = get_option($agency . '_banner_image');
    $banner_link = get_option($agency . '_banner_link');

    $data['banner_image'] = get_option($agency . '_banner_image');
    $data['banner_link'] = get_option($agency . '_banner_link');

    return $data;
  }
}
