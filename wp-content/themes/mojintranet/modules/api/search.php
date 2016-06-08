<?php if (!defined('ABSPATH')) die();

/** Search API
 * Features:
 * - get a list of results matching given criteria
 */
class Search_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    add_filter('relevanssi_match', array(&$this, 'exact_title_matches_filter'));
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_results();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'agency' => $params[0],
      'additional_filters' => $params[1],
      'post_type' => $params[2],
      'keywords' => $params[3],
      'page' => $params[4],
      'per_page' => $params[5]
    );
  }

  protected function get_results() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->search->get($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }

  public function exact_title_matches_filter($match) {
    $search_query = urldecode(strtolower($this->get_param('keywords')));
    if ($search_query == strtolower(get_the_title($match->doc))) {
      $match->weight = $match->weight * 10;
    }

    return $match;
  }
}
