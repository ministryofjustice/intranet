<?php if (!defined('ABSPATH')) die();

abstract class MVC_loader {

  function __construct(){
  }

  function model($name){
    //!!! TO BE IMPLEMENTED
  }

  function view($path, $data = []){
    foreach($data as $key=>$value){
      $$key = $value;
    }

    include(MVC_VIEWS_DIR.$path.'.php');
  }
}
