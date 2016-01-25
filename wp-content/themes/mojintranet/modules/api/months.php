<?php if (!defined('ABSPATH')) die();

class Months_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_months();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
    );
  }

  protected function get_months() {
    $data = $this->MVC->model->months->get_list($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }
}
