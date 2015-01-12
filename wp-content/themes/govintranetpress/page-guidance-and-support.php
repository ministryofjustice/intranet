<?php if (!defined('ABSPATH')) die();

/**
 * The Template for guidance and support pages
 *
 * Template name: Guidance & Support
 */
class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      get_footer();
    }
  }

  function get_data(){
    return array(
    );
  }
}

new Page_guidance_and_support();
