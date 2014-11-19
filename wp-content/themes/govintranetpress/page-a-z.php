<?php if (!defined('ABSPATH')) die();
/* Template name: A-Z */

class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/a_z/main', $this->get_data());
      get_footer();
    }
  }

  function get_data(){
    return array(
      'title' => get_the_title(),
      'letters' => explode(';', 'All;A;B;C;D;E;F;G;H;I;J;K;L;M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z')
    );
  }
}

new Page_guidance_and_support();

