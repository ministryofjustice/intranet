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
      $this->view('pages/guidance_and_support_content/main', $this->get_data());
      get_footer();
    }
  }

  function get_data(){
    return array(
      'redirect_url' => get_post_meta(get_the_ID(), 'redirect_url', true),
      'redirect_enabled' => get_post_meta(get_the_ID(), 'redirect_enabled', true)
    );
  }
}

new Page_guidance_and_support();
