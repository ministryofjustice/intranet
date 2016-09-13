<?php if (!defined('ABSPATH')) die();

/** News API
 * Features:
 * - get a list of news
 */
class News_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->MVC->model('post_siblings');
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    $method = $this->get_param('method');

    switch ($this->get_method()) {
      case 'GET':
        switch ($method) {
          case 'get':
            $this->get_news();
            break;

          case 'siblings':
            $this->get_sibling_links();
            break;
        }
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $method = get_array_value($params, 0, 'get');

    $this->params = array(
      'method' => $method,
    );

    $this->params['agency'] = get_array_value($params, 1, 'hq');
    $this->params['additional_filters'] = get_array_value($params, 2, '');

    if ($method == 'get') {
      $this->params['date'] = get_array_value($params, 3, '');
      $this->params['keywords'] = get_array_value($params, 4, '');
      $this->params['page'] = get_array_value($params, 5, 1);
      $this->params['per_page'] = get_array_value($params, 6, 10);
    }
    else if ($method == 'siblings'){
      $this->params['post_id'] = get_array_value($params, 3, null);
    }
  }

  protected function get_news() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);

    $data = $this->MVC->model->news->get_list($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }

  protected function get_sibling_links() {
    $options = $this->params;
    $data = $this->MVC->model->post_siblings->get_post_sibling_links($options);
    $this->response($data, 200, 300);
  }
}
