<?php if (!defined('ABSPATH')) die();

class Children_API extends API {
  public function __construct($params) {
    parent::__construct($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_children($this->get_param(0));
        break;

      default:
        break;
    }
  }

  protected function get_children($page_id) {
    $children = $this->MVC->model->children->get_all();
    $this->response(['test'], 200);
  }
}
