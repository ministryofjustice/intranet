<?php if (!defined('ABSPATH')) die();

/**
 * The Template for annual leave page
 *
 * Template name: Annual leave
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
class Page_annual_leave extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/annual_leave/main', $this->get_data());
      get_footer();
    }
  }

  function get_data(){
    $article_date = get_the_date();

    ob_start();
    the_content();
    $content = ob_get_clean();

    $this_id = $post->ID;

    return array(
      'id' => $this_id,
      'author' => get_the_author(),
      'title' => get_the_title(),
      'excerpt' => get_the_excerpt(),
      'content' => $content,
      'raw_date' => $article_date,
      'human_date' => date("j F Y", strtotime($article_date))
    );
  }
}

new Page_annual_leave();
