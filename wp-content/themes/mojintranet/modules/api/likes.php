<?php if (!defined('ABSPATH')) die();

/** Likes API
 * Features:
 * - get a number of likes for a post / comment
 * - increment likes of a post / comment
 */
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
      'content_type' => $params[0],
      'id' => $params[1]
    );
  }

  protected function read() {
    $data = $this->MVC->model->likes->read($this->params['content_type'],$this->params['id']);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

  protected function update() {
    $data = $this->MVC->model->likes->update($this->params['content_type'],$this->params['id'], $this->put('nonce'));
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }
}
