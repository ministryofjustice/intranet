<?php if (!defined('ABSPATH')) die();

/** Menu API
 * Features:
 * - Get a list of menu items
 */
class Menu_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->MVC->model('menu');
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->get_menu_items();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'location' => (string) $params[0],
      'depth_limit' => isset($params[1]) ? (int) $params[1] : 0
    );
  }

  protected function get_menu_items() {
    $data = $this->MVC->model->menu->get_menu_items($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 120);
  }
}
