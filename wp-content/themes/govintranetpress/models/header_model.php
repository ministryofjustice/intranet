<?php if (!defined('ABSPATH')) die();

class Header_model extends MVC_model {
  function get_data() {
    return array(
      'my_moj' => $this->my_moj_model->get_data()
    );
  }
}
