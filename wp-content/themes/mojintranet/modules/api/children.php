<?php if (!defined('ABSPATH')) die();

class Children_API extends API {
  public function __construct($params) {
    parent::__construct($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_children();
        break;

      default:
        break;
    }
  }

  protected function get_children() {
    $data = $this->MVC->model->children->get_all($this->get_param(0), $this->get_param(1), $this->get_param(2));
    $this->response($data, 200);
  }
}
