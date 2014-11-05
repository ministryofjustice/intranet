<?php if (!defined('ABSPATH')) die();

abstract class MVC_loader {
  function __construct(){
    //determine views directory
    $info = new ReflectionClass($this);
    $instance_dir = dirname($info->getFileName());
    $is_plugin = strpos($instance_dir, WP_PLUGIN_DIR)===0;
    $this->views_dir = $is_plugin ? $instance_dir.'/'.MVC_VIEWS_DIR : MVC_VIEWS_PATH;
  }

  function model($name){
    //!!! TO BE IMPLEMENTED
  }

  function view($path, $data = array()){
    foreach($data as $key=>$value){
      $$key = $value;
    }
    include($this->views_dir.$path.'.php');
  }
}
