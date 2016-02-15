<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {
  function __construct($param_string = ''){
    parent::__construct();

    $this->_get_segments($param_string);

    ob_start();
    wp_head();
    $this->wp_head = ob_get_clean();

    if($this->is_plugin) {
      $this->main();
    }
  }

  private function _get_segments($param_string) {
    $segments = explode('/', $param_string);
    $this->method = array_shift($segments) ?: 'main';
    $this->segments = $segments;
  }

  public function run() {
    call_user_func_array(array($this, $this->method), $this->segments);
  }

  public function load_models() {
    //!!! TODO: loading the global models here. These should be auto-loaded based on config in the future
    $this->model('my_moj');
    $this->model('header');
    $this->model('breadcrumbs');
    $this->model('search');
    $this->model('children');
    $this->model('news');
    $this->model('events');
    $this->model('likes');
    $this->model('months');
    $this->model('post');
  }
}
