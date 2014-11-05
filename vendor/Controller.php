<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {
  function __construct(){
    parent::__construct();
    
    if(method_exists($this, 'main')){
      $this->main();
    }
  }
}
