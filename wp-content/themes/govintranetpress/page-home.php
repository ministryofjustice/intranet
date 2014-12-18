<?php
/**
 * The template for displaying Search Results pages.
 *
 * Template name: Home page
 */
class Page_home extends MVC_controller {
  function main() {
    get_header();
    if(have_posts()) the_post();
    $this->view('pages/homepage/main', $this->get_data());
    get_footer();
  }

  private function get_data() {
    return array(
      'emergency_message' => $this->get_emergency_message()
    );
  }

  private function get_emergency_message() {
    $message = get_option("homepage_control_emergency_message");
    $message = apply_filters('the_content', $message, true);

    return array(
      'message' => $message,
      'type' => strtolower(get_option("homepage_control_emergency_message_style"))
    );
  }
}

new Page_home();
