<?php if (!defined('ABSPATH')) die();

/**
 * The generic template with LHS navigation
 *
 * Template name: Generic nav
 */

class Page_generic_nav extends MVC_controller {
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

    ob_start();
    the_content();
    $content = ob_get_clean();

    return array(
      'page' => 'pages/generic_nav/main',
      'template_class' => 'generic-nav',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'children_data' => $this->get_children_data()
      )
    );
  }

  private function get_children_data() {
    $id = $this->post_ID;
    $children = array();

    do {
      array_push($children, $this->get_children_from_API($id));
    }
    while($id = wp_get_post_parent_id($id));

    $children = array_reverse($children);

    $top_level = $this->get_children_from_API();
    $top_level['title'] = 'MoJ Intranet';

    array_unshift($children, $top_level);

    return htmlspecialchars(json_encode($children));
  }

  private function get_children_from_API($id = null) {
    return $this->model->children->get_all($id);
  }
}
