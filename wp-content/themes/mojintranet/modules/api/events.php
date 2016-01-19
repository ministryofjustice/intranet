<?php if (!defined('ABSPATH')) die();

class Events_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_events();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'date' => $params[0],
      'keywords' => $params[1],
      'page' => $params[2] ?: 1,
      'per_page' => $params[3] ?: 10
    );
  }

  protected function get_events() {
    $data = $this->MVC->model->events->get_list($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }
}
