<?php if (!defined('ABSPATH')) die();

class Homepage_banner_side_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function get($options = []) {
    $data = [];
    $agency = get_array_value($options, 'agency', 'hq');
    $data['image_url'] = get_option($agency . '_banner_image_side');
    $data['url'] = get_option($agency . '_banner_link_side');
    $data['alt'] = get_option($agency . '_banner_alt_side');
    $data['text'] = get_option($agency . '_banner_image_side_title');
    //$data['visible'] = (int) get_option($agency . '_banner_image_side_title_enable');

    return array(
        'results' => $data
    );
  }
}
