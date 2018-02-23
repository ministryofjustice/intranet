<?php if (!defined('ABSPATH')) die();

class Page_regional_events extends MVC_controller {
  private $post;

  function main() {
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    $this->post = get_post();

    ob_start();
    the_content();
    $content = ob_get_clean();

    return [
      'page' => 'pages/regional_events_landing/main',
      'template_class' => 'regional-events-landing',
      'breadcrumbs' => true,
      'page_data' => [
        'id' => $this->post_id,
        'title' => get_the_title(),
        'content' => $content,
        'template_uri' => get_template_directory_uri(),
        'page_base_url' => get_permalink($this->post_id),
        'region' => get_the_terms($this->post_id, 'region')[0]->slug,
      ]
    ];
  }
}
