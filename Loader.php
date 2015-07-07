<?php if (!defined('ABSPATH')) die();

abstract class MVC_loader {
  public static $models = array();

  function __construct() {
    //determine views and models directories
    $info = new ReflectionClass($this);
    $instance_dir = realpath(dirname($info->getFileName()));
    $this->is_plugin = strpos($instance_dir, realpath(WP_PLUGIN_DIR)) === 0;
    $this->views_dir = $this->is_plugin ? $instance_dir.'/'.MVC_VIEWS_DIR : MVC_VIEWS_PATH;
    $this->models_dir = $this->is_plugin ? $instance_dir.'/'.MVC_MODELS_DIR : MVC_MODELS_PATH;
  }

  function model($name) {
    $name = $name . '_model';
    $model_name = ucfirst($name);

    if(!in_array($name, self::$models)) {
      include_once($this->models_dir.$name.'.php');

      $instance = new $model_name;
      $this->$name = $instance;
      self::$models[$name] =& $instance;
    }
  }

  function view($path, $data = array(), $return_as_string = false) {
    if(is_array($data)) {
      foreach($data as $key=>$value){
        $$key = $value;
      }
    }

    ob_start();
    include($this->views_dir.$path.'.php');
    $html = ob_get_clean();

    if($return_as_string) {
      return $html;
    }
    else {
      echo $html;
      return null;
    }
  }
}
