<?php if (!defined('ABSPATH')) die();

/** Page tree API
 * Features:
 * - Get a list of children posts belonging to a specific post parent
 */
class Page_tree_API extends API {
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
          case 'children':
            $this->get_children();
            break;

          case 'ancestors':
            $this->get_ancestors();
            break;
        }
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'method' => $params[0],
      'agency' => $params[1],
      'additional_params' => $params[2],
      'page_id' => (int) $params[3],
      'depth' => (int) $params[4],
      'order' => $params[5]
    );
  }

  protected function get_children() {
    $options = $this->add_taxonomies($this->params);
    $data = $this->MVC->model->page_tree->get_children($options);
    //$data['url_params'] = $this->params;
    $this->response($data, 200, 120);
  }

  protected function get_ancestors() {
    $options = $this->add_taxonomies($this->params);
    $data = $this->MVC->model->page_tree->get_ancestors($options);
    //$data['url_params'] = $this->params;
    $this->response($data, 200, 120);
  }
}
