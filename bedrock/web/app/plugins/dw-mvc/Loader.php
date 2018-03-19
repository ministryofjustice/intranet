<?php if (!defined('ABSPATH')) die();

abstract class MVC_loader {
  public $model;
  //stores global view variables assigned by add_global_view_var()
  //and add_global_view_data() (see controller)
  protected $global_view_data = [];

  const DS = '/';

  function __construct() {
    $this->model = new stdClass();

    $this->settings = [
      'views_dir' => 'views',
      'models_dir' => 'models'
    ];

    $this->template_path = get_template_directory() . $this::DS;
    $this->views_path = $this->template_path . $this->settings['views_dir'] . $this::DS;
    $this->models_path = $this->template_path . $this->settings['models_dir'] . $this::DS;
  }

  public function model($name) {
    $class_name = ucfirst($name . '_model');

    if (!method_exists($this->model, $name)) {
      include_once($this->models_path . $name . '.php');

      $instance = new $class_name;
      $this->model->$name =& $instance;
    }
  }

  public function view($path, $data = [], $return_as_string = false) {
    //assign global view data first
    foreach ($this->global_view_data as $key => $value) {
      $$key = $value;
    }

    //then assign the specified data
    if (is_array($data)) {
      foreach ($data as $key => $value) {
        $$key = $value;
      }
    }

    ob_start();
    include($this->views_path.$path.'.php');
    $html = ob_get_clean();

    if ($return_as_string) {
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
    $this->model('agency');
    $this->model('breadcrumbs');
    $this->model('content');
    $this->model('events');
    $this->model('header');
    $this->model('hierarchy');
    $this->model('menu');
    $this->model('my_moj');
    $this->model('need_to_know');
    $this->model('page_tree');
    $this->model('post');
    $this->model('search');
    $this->model('user');
  }
}
