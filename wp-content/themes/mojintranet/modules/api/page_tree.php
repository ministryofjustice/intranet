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

          case 'children-by-tag':
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
    $method = $params[0];

    $this->params = array(
      'method' => $method,
    );

    if($method === 'children-by-tag') {
      $this->params['tag'] = $params[1];
      $this->params['depth'] = (int) $params[2];
      $this->params['order'] = $params[3];
    }
    else {
      $this->params['agency'] = $params[1];
      $this->params['additional_params'] = $params[2];
      $this->params['page_id'] = (int) $params[3];
      $this->params['depth'] = (int) $params[4];
      $this->params['order'] = $params[5];
    }
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
