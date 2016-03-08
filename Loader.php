<?php if (!defined('ABSPATH')) die();

abstract class MVC_loader {
  public $model;

  function __construct() {
    $this->model = new stdClass();

    //determine views and models directories
    $info = new ReflectionClass($this);
    $instance_dir = realpath(dirname($info->getFileName()));
    $this->is_plugin = strpos($instance_dir, realpath(WP_PLUGIN_DIR)) === 0;
    $this->views_dir = $this->is_plugin ? $instance_dir.'/'.MVC_VIEWS_DIR : MVC_VIEWS_PATH;
    $this->models_dir = $this->is_plugin ? $instance_dir.'/'.MVC_MODELS_DIR : MVC_MODELS_PATH;
  }

  public function model($name) {
    $class_name = ucfirst($name . '_model');

    if(!method_exists($this->model, $name)) {
      include_once($this->models_dir . $name . '.php');

      $instance = new $class_name;
      $this->model->$name =& $instance;
    }
  }

  public function view($path, $data = array(), $return_as_string = false) {
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

  public function get_model_object() {
    return $this->model;
  }

  protected function _load_default_models() {
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
    $this->model('need_to_know');
    $this->model('comments');
  }
}
