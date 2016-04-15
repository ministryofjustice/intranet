<?php if (!defined('ABSPATH')) die();

/** Months API
 * Features:
 * - get a list of 12 consecutive months (starting with current month) with a number of events taking place in each month
 */
class Months_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_months();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
    );
  }

  protected function get_months() {
    $options = $this->params;
    $options['tax_query'] = $this->get_taxonomies();
    $data = $this->MVC->model->months->get_list($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 300);
  }
}
