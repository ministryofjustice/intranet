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
      'agency' => $params[0],
      'additional_filters' => $params[1],
      'date' => $params[2],
      'keywords' => $params[3],
      'page' => $params[4] ?: 1,
      'per_page' => $params[5] ?: 10
    );
  }

  protected function get_posts() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->post->get_list($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }
}
