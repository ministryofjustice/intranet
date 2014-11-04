<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {
  function __construct(){
    if(method_exists($this, 'main')){
      $this->main();
    }
  }
}
