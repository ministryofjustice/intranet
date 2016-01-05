<?php if (!defined('ABSPATH')) die();

class Children_API extends API {
  public function __construct($params) {
    parent::__construct($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_children($this->get_param(0), $this->get_param(1), $this->get_param(2));
        break;

      default:
        break;
    }
  }

  protected function get_children($page_id, $order_by, $order) {
    $children = $this->MVC->model->children->get_all($page_id, $order_by, $order);
    $this->response($children, 200);
  }
}
