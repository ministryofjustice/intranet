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
      'post_type' => $params[0],
      'category' => $params[1],
      'keywords' => $params[2],
      'page' => $params[3],
      'per_page' => $params[4]
    );
  }

  protected function get_results() {
    $data = $this->MVC->model->search->get($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

  public function exact_title_matches_filter($match) {
    $search_query = urldecode(strtolower($this->get_param('keywords')));
    if ($search_query == strtolower(get_the_title($match->doc))) {
      $match->weight = $match->weight * 10;
    }

    return $match;
  }
}
