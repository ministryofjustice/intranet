<?php if (!defined('ABSPATH')) die();

class Banner_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_emergency_banner();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array();
  }

  private function get_emergency_banner() {
    $data = $this->MVC->model->emergency_banner->get_emergency_banner($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60);
  }

}