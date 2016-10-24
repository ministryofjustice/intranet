<?php if (!defined('ABSPATH')) die();

class Header_model extends MVC_model {
  function get_data() {
    return array(
      'stringified_agencies' => htmlspecialchars(json_encode($this->model->agency->get_list())),
      'main_menu' => $this->model->menu->get_menu_items([
        'location' => 'main-menu',
        'post_id' => true
      ])
    );
  }
}
