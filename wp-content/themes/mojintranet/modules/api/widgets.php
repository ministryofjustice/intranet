<?php if (!defined('ABSPATH')) die();

class Widgets_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->params['widget']) {
      case 'featured-news':
        $this->get_featured_news();
        break;

      case 'need-to-know':
        $this->get_need_to_know();
        break;

      case 'quick-links':
        $this->get_quick_links();
        break;

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $this->params = array(
      'widget' => $params[0],
      'start' => (int) $params[1],
      'length' => (int) $params[2]
    );
  }

  private function get_featured_news() {
    $data = $this->MVC->model->news->get_featured($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

  private function get_need_to_know() {
    $data = $this->MVC->model->need_to_know->get_need_to_know($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

  private function get_quick_links() {
    $data = $this->MVC->model->my_moj->get_quick_links($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

}