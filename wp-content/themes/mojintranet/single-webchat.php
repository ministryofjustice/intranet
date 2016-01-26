<?php if (!defined('ABSPATH')) die();

/**
 * The generic template with LHS navigation
 *
 * Template name: Webchat template
 */

class Single_webchat extends MVC_controller {
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
      'page' => 'pages/webchat_single/main',
      'template_class' => 'webchat-single',
      'cache_timeout' => 60 * 5, /* 5 minutes */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'children_data' => $this->get_children_data(),
        'coveritlive_id' => get_post_meta($this->post_ID, '_webchat-coveritlive-id', true)
      ),
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
