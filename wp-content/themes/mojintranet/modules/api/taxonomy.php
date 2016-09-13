<?php if (!defined('ABSPATH')) die();

/** Taxonomy API
 * Features:
 * - get a list of terms
 */
class Taxonomy_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->MVC->model('taxonomy');
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_terms();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'agency' => $params[0],
      'additional_filters' => $params[1],
      'taxonomy' => $params[2],
      'hide_empty' => $params[3],
    );
  }

  protected function get_terms() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data['terms'] = $this->MVC->model->taxonomy->get($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }
}
