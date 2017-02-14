<?php if (!defined('ABSPATH')) die();

class Homepage_banner_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get($options = []) {
    $data = [];
    $agency = get_array_value($options, 'agency', 'hq');
    $data['image_url'] = get_option($agency . '_banner_image');
    $data['url'] = get_option($agency . '_banner_link');

    return array(
        'results' => $data
    );
  }
}
