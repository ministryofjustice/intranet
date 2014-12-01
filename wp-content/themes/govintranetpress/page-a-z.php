<?php if (!defined('ABSPATH')) die();
/* Template name: A-Z */

class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      if($_GET['v2']){
        $this->view('pages/a_z/main2', $this->get_data());
      }
      else{
        $this->view('pages/a_z/main', $this->get_data());
      }
      get_footer();
    }
  }

  function get_data(){
    $results = array(
      array(
        'title' => 'Annual leave',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Annual leave form',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Absence without leave form',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Disability leave',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Parental leave',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Parental leave form',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Holiday',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Annual leave',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Annual leave form',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Absence without leave form',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Disability leave',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Parental leave',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Parental leave form',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Holiday',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      )
    );

    return array(
      'title' => get_the_title(),
      'letters' => explode(';', 'All;A;B;C;D;E;F;G;H;I;J;K;L;M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z'),
      'results' => $results
    );
  }
}

new Page_guidance_and_support();
