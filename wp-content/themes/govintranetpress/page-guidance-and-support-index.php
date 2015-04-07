<?php if (!defined('ABSPATH')) die();
/* Template name: Guidance & Support Index */

class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      //$this->view('shared/breadcrumbs');
      $this->view('pages/guidance_and_support/main');
      get_footer();
    }
  }
}

new Page_guidance_and_support();
