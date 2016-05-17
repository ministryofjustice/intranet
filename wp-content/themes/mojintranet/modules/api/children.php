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
      'agency' => $params[0],
      'additional_params' => $params[1],
      'page_id' => (int) $params[2],
      'order' => $params[3]
    );
  }

  protected function get_children() {
    $options = $this->add_taxonomies($this->params);
    $data = $this->MVC->model->children->get_data_recursive($options);
    //$data['url_params'] = $this->params;
    $this->response($data, 200, 120);
  }
}
