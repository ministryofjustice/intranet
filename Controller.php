<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {
  function __construct($param_string = ''){
    global $MVC;

    parent::__construct();

    if(!$MVC) {
      $MVC = $this;
      $this->_load_default_models();
    }

    $this->_get_segments($param_string);
    $this->_get_wp_header();

    if($this->is_plugin) {
      $this->main();
    }
  }

  public function run() {
    call_user_func_array(array($this, $this->method), $this->segments);
  }

  public function output_json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
  }

  private function _get_segments($param_string) {
    $segments = explode('/', $param_string);
    $this->method = array_shift($segments) ?: 'main';
    $this->segments = $segments;
  }

  private function _get_wp_header() {
    _wp_admin_bar_init();
    ob_start();
    wp_head();
    $this->wp_head = ob_get_clean();
  }
}
