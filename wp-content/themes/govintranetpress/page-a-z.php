<?php if (!defined('ABSPATH')) die();

/**
 * Template name: A-Z
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
class Page_single_news extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/a_z/main', $this->get_data());
      get_footer();
    }
  }

  private function get_data(){
    $results = array(
      array(
        'title' => 'Annual leave',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Annual leave form',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Absence without leave form',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Disability leave',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Parental leave',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Parental leave form',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      ),

      array(
        'title' => 'Holiday',
        'date' => '2014-11-7',
        'category' => 'Guidance',
        'breadcrumbs' => 'HQ &gt; HR',
        'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh. Nunc varius facilisis eros.'
      )
    );

    //add formatted dates
    foreach($results as $key=>$result){
      $results[$key]['human_date'] = date("j F Y", strtotime($result['date']));
    }

    return array(
      'results' => $results,
      'prev_page_exists' => false,
      'next_page_exists' => false
    );
  }
}

new Page_single_news();
