<?php if (!defined('ABSPATH')) die();

/** Post API
 * Features:
 * - get a list of posts
 */
class Post_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_posts();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'category' => $params[0],
      'date' => $params[1],
      'keywords' => $params[2],
      'page' => $params[3] ?: 1,
      'per_page' => $params[4] ?: 10
    );
  }

  protected function get_posts() {
    $options = $this->params;
    $options['tax_query'] = $this->get_taxonomies();
    $data = $this->MVC->model->post->get_list($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }
}
