<?php
/**
 * The template for displaying Search Results pages.
 *
 * Template name: Home page
 */
class Page_home extends MVC_controller {
  function __construct() {
    echo'asd';
    parent::__construct();

    $this->model('my_moj');
    //Debug::full($this);
    //Debug::full($this->my_moj_model);
    echo'1';
    //Debug::full($this->my_moj_model->get_data());
  }

  function main() {
    echo'2';
    //Debug::full($this->my_moj_model->get_data());
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    return array(
      'page' => 'pages/homepage/main',
      'page_data' => array(
        'emergency_message' => $this->get_emergency_message(),
        //'my_moj' => $this->my_moj_model->get_data()
      )
    );
  }

  private function get_emergency_message() {
    $visible = get_option("emergency_toggle");
    $title = get_option("emergency_title");
    $date = get_option("emergency_date");
    $message = get_option("homepage_control_emergency_message");
    $message = apply_filters('the_content', $message, true);
    $type = get_option("emergency_type");

    return array(
      'visible'     => $visible,
      'title'       => $title,
      'date'        => $date,
      'message'     => $message,
      'type'        => $type
    );
  }
}
