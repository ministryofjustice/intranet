<?php if (!defined('ABSPATH')) die();

/**
 * Controller for regional news (updates).
 *
 */

class Page_regional_news extends MVC_controller {
  function main() {
    $this->post = get_post();
    $this->view('layouts/default', $this->get_data());
  }

  function get_data() {
    ob_start();
    the_content();
    $content = ob_get_clean();

    $top_slug = $this->post->post_name;

    return [
      'page' => 'pages/regional_updates_landing/main',
      'template_class' => 'regional-updates-landing',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => [
        'id' => $this->post_id,
        'title' => get_the_title(),
        'template_uri' => get_template_directory_uri(),
        'page_base_url' => get_permalink($this->post_id),
        'region' => get_the_terms($this->post_id, 'region')[0]->slug,
        'content' => $content
      ]
    ];
  }

}
