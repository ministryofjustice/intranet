<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {
  function __construct($param_string = ''){
    global $MVC;

    parent::__construct();

    if (!$MVC) {
      $MVC = $this;
      $this->_load_default_models();
    }

    $this->_get_segments($param_string);
    $this->wp_head = $this->_get_wp_header();
  }

  public function run() {
    if (method_exists($this, $this->method)) {
      call_user_func_array([$this, $this->method], $this->segments);
    }
    else {
      header("Location: " . site_url());
    }
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
    return ob_get_clean();
  }
}
