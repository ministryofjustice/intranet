<?php if (!defined('ABSPATH')) die();

/**
 * The default template with/without LHS navigation
 *
 * Template name: Default
 */

class Page_generic extends MVC_controller {
  function main(){
    $this->model('my_moj');

    while(have_posts()){
      the_post();

      $this->post_ID = get_the_ID();

      $this->view('layouts/default', $this->get_data());
    }
  }

function get_data(){
    $post = get_post($this->post_ID);

    $authors = dw_get_author_info($this->post_ID);
    $agencies = get_the_terms($this->post_ID, 'agency');
    $list_of_agencies = [];

    foreach ($agencies as $agency) {
      $list_of_agencies[] = $agency->name;
    }

    ob_start();
    the_content();
    $content = ob_get_clean();

    $post_type = get_post_type( get_the_ID() );
      
    if ($post_type == 'regional_page') {
        $lhs_menu_on = true;
    } else {
        $lhs_menu_on = get_post_meta($this->post_ID, 'dw_lhs_menu_on', true) == 1 ? true : false;
    }
      
    if ($lhs_menu_on) {
        $content_classes = 'col-lg-9 col-md-8 col-sm-12';
    } else {
        $content_classes = 'col-lg-9 col-md-12 col-sm-12';
    }
      
    return array(
      'page' => 'pages/generic/main',
      'template_class' => 'generic',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'agencies' => implode(', ', $list_of_agencies),
        'author' => $authors[0]['name'],
        'last_updated' => date("j F Y", strtotime(get_the_modified_date())),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'content_classes' => $content_classes,
        'lhs_menu_on' => $lhs_menu_on,
        'hide_page_details' => (boolean) get_post_meta($this->post_ID, 'dw_hide_page_details', true)
      )
    );
  }
}
