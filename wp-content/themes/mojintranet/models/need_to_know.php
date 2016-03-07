<?php if (!defined('ABSPATH')) die();

class Need_to_know_model extends MVC_model {
  public function __construct() {
    parent::__construct();

    $this->max_need_to_know_news = 3;
  }

  public function get_need_to_know($options = array()) {
    $options = $this->normalize_need_to_know_options($options);
    for($a = $options['start']; $a <= $options['length']; $a++) {
      $slide['title'] = get_option('need_to_know_headline' . $a)?:'';
      $slide['url'] = get_option('need_to_know_url' . $a)?:'';
      $slide['image_url'] = $this->get_correct_image(get_option('need_to_know_image' . $a))?:'';
      $slide['image_alt'] = get_option('need_to_know_alt' . $a)?:'';
      $data['results'][] = $slide;
    }

    return $data;
  }

  private function normalize_need_to_know_options($options) {
    $default = array(
      'start' => 1,
      'length' => $this->max_need_to_know_news
    );

    foreach($options as $key=>$value) {
      if($value) {
        if($key=='length' && $value>$this->max_need_to_know_news) {
          $default[$key] = $this->max_need_to_know_news;
        } else {
          $default[$key] = $value;
        }
      }
    }

    return $default;
  }

  private function get_correct_image($url) {
    $url = preg_replace('#https://s3-eu-west-1.amazonaws.com/moj-wp-prod/[^/]+#', site_url(), $url);
    $url = preg_replace('#http://[^/]+#', site_url(), $url);
    $attachment_id = get_attachment_id_from_url($url);
    $thumbnail = wp_get_attachment_image_src($attachment_id, 'need-to-know');
    return $thumbnail[0];
  }

}
