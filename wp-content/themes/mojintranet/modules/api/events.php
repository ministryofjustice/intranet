<?php if (!defined('ABSPATH')) die();

/** Events API
 * Features:
 * - get a list of events
 */
class Events_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_events();
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

  protected function get_events() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->events->get_list($options);
    $months = $this->MVC->model->months->get_list($this->add_taxonomies());
    $data['months'] = $months['results'];
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }
}
