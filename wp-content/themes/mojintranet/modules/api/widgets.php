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
        $this->get_news(true);
        break;

      case 'non-featured-news':
        $this->get_news();
        break;

      case 'need-to-know':
        $this->get_need_to_know();
        break;

      case 'my-moj':
        $this->get_my_moj();
        break;

      case 'follow-us':
        $this->get_follow_us_links();
        break;

      default:
        $this->error('Invalid widget');
        break;
    }
  }

  protected function parse_params($params) {
    $widget = $params[0];

    $this->params = array(
      'widget' => $widget,
      'agency' => $params[1],
      'additional_filters' => $params[2]
    );

    if($widget == 'my-moj' || $widget == 'follow-us') {
      $this->params['start'] = (int) $params[3];
      $this->params['length'] = (int) $params[4];
    }
  }

  private function get_news($featured = false) {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->news->get_widget_news($options, $featured);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60);
  }

  private function get_need_to_know() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->need_to_know->get_data($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60);
  }

  private function get_my_moj() {
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->my_moj->get_data($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60 * 60);
  }

  private function get_follow_us_links() {
    $this->MVC->model('follow_us');
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $data = $this->MVC->model->follow_us->get_data($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60 * 60);
  }
}
