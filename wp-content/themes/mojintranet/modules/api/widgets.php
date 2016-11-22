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

      case 'all':
        $this->get_all();
        break;

      case 'regional':
        $this->get_regional();
        break;

      default:
        $this->error('Invalid widget');
        break;
    }
  }

  protected function parse_params($params) {
    $widget = get_array_value($params, 0, '');

    $this->params = array(
      'widget' => $widget,
      'agency' => get_array_value($params, 1, 'hq'),
      'additional_filters' => get_array_value($params, 2, '')
    );

    if($widget == 'my-moj' || $widget == 'follow-us') {
      $this->params['start'] = (int) get_array_value($params, 3, 0);
      $this->params['length'] = (int) get_array_value($params, 4, 10);
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
    $data = $this->MVC->model->need_to_know->get_data($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60);
  }

  private function get_my_moj() {
    $options = $this->params;
    $data = $this->MVC->model->my_moj->get_data($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60 * 60);
  }

  private function get_follow_us_links() {
    $this->MVC->model('follow_us');
    $options = $this->params;
    $data = $this->MVC->model->follow_us->get_data($options);
    $data['url_params'] = $this->params;
    $this->response($data, 200, 60 * 60);
  }

  private function get_all() {
    $data = [];

    //news list
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $options['start'] = 0;
    $options['length'] = 8;
    $data['news_list'] = $this->MVC->model->news->get_widget_news($options, false);

    //featured news
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $options['start'] = 0;
    $options['length'] = 2;
    $data['featured_news'] = $this->MVC->model->news->get_widget_news($options, true);

    //events
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $options['page'] = 1;
    $options['per_page'] = 2;
    $data['events'] = $this->MVC->model->events->get_list($options);

    //posts
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $options['page'] = 1;
    $options['per_page'] = 5;
    $data['posts'] = $this->MVC->model->post->get_list($options);

    //need to know
    $options = $this->params;
    $data['need_to_know'] = $this->MVC->model->need_to_know->get_data($options);

    //my moj
    $options = $this->params;
    $data['my_moj'] = $this->MVC->model->my_moj->get_data($options);

    //folow us
    $options = $this->params;
    $this->MVC->model('follow_us');
    $data['follow_us'] = $this->MVC->model->follow_us->get_data($options);

    //emergency message
    $options = $this->params;
    $data['emergency_message'] = $this->MVC->model->emergency_banner->get($options);

    $data['url_params'] = $this->params;
    $this->response($data, 200, 60);
  }

  private function get_regional() {
    $data = [];

    //news list
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $options['start'] = 0;
    $options['length'] = 8;
    $data['news_list'] = $this->MVC->model->news->get_widget_news($options, false);

    //events
    $options = $this->params;
    $options = $this->add_taxonomies($options);
    $options['page'] = 1;
    $options['per_page'] = 2;
    $data['events'] = $this->MVC->model->events->get_list($options);

    $data['url_params'] = $this->params;
    $this->response($data, 200, 60);
  }
}
