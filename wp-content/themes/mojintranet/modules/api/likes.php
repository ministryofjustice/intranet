<?php if (!defined('ABSPATH')) die();

class Likes_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->read();
        break;

      case 'PUT':
        $this->update();

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'id' => $params[0]
    );
  }

  protected function read() {
    $data = $this->MVC->model->news->read($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

  protected function update() {
    $data = $this->MVC->model->news->update($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }
}
