<?php if (!defined('ABSPATH')) die();

/** Children API
 * Features:
 * - Get a list of children posts belonging to a specific post parent
 */
class Children_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
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

  protected function parse_params($params) {
    $this->params = array(
      'page_id' => (int) $params[0],
      'order' => $params[1]
    );
  }

  protected function get_children() {
    $data = call_user_func_array(array($this->MVC->model->children, 'get_all'), $this->params);
    //$data['url_params'] = $this->params;
    $this->response($data, 200, 120);
  }
}
