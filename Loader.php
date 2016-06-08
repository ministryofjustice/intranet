<?php if (!defined('ABSPATH')) die();

abstract class MVC_loader {
  public $model;

  function __construct() {
    $this->model = new stdClass();

    $this->template_path = get_template_directory().'/';
    $this->views_dir = 'views/';
    $this->views_path = $this->template_path.$this->views_dir;
    $this->models_dir = 'models/';
    $this->models_path = $this->template_path.$this->models_dir;
  }

  public function model($name) {
    $class_name = ucfirst($name . '_model');

    if(!method_exists($this->model, $name)) {
      include_once($this->models_path . $name . '.php');

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
    include($this->views_path.$path.'.php');
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
    $this->model('emergency_banner');
  }
}
