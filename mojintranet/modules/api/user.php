<?php if (!defined('ABSPATH')) die();

class User_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    $method = $this->get_param('method');

    switch ($this->get_method()) {
      case 'GET':
        switch ($method) {
          case 'status':
            $this->get_status();
            break;
        }
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = [
      'method' => $params[0]
    ];
  }

  protected function get_status() {
    $options = $this->params;
    $data = $this->MVC->model->user->get_status();
    $this->response($data, 200, 0);
  }
}
