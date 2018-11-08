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
      'agency' => get_array_value($params, 0, 'hq'),
      'additional_filters' => get_array_value($params, 1, ''),
      'taxonomy' => get_array_value($params, 2, ''),
      'hide_empty' => get_array_value($params, 3, false),
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
