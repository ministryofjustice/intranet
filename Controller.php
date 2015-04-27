<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {
  function __construct(){
    parent::__construct();

    ob_start();
    wp_head();
    $this->wp_head = ob_get_clean();

    //!!! TODO: loading the global models here. These should be auto-loaded based on config in the future
    if(!$this->is_plugin) {
      $this->model('my_moj');
      $this->model('header');
    }
  }
}
