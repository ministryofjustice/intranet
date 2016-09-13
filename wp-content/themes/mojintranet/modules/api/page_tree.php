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
          case 'guidance-index':
            $this->get_guidance_index();
            break;
        }
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $method = get_array_value($params, 0, 'children');

    $this->params = [
      'method' => $method,
    ];

    $this->params['agency'] = get_array_value($params, 1, 'hq');
    $this->params['additional_params'] = get_array_value($params, 2, '');

    if ($method !== 'guidance-index') {
      $this->params['depth'] = (int) get_array_value($params, 4, 1);
      $this->params['order'] = get_array_value($params, 5, 'asc');

      if ($method === 'children-by-tag') {
        $this->params['tag'] = get_array_value($params, 3, '');
      }
      else {
        $this->params['page_id'] = (int) get_array_value($params, 3, 0);
      }
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

  protected function get_guidance_index() {
    $options = $this->add_taxonomies($this->params);
    $data = $this->MVC->model->page_tree->get_guidance_index($options);
    //$data['url_params'] = $this->params;
    $this->response($data, 200, 120);
  }
}
