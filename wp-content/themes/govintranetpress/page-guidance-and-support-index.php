<?php if (!defined('ABSPATH')) die();
/* Template name: Guidance & Support Index */

class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('pages/guidance_and_support/main', $this->get_data());
      get_footer();
    }
  }

  private function get_data() {
    return array(
      'title' => get_the_title()
    );
  }
}

new Page_guidance_and_support();
