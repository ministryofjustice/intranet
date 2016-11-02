<?php if (!defined('ABSPATH')) die();
/* Template name: About us */

class Page_about_us extends MVC_controller {
  function main() {
    while (have_posts()) {
      the_post();

      $this->post_ID = get_the_ID();
      $this->post = get_post($this->post_ID);
      $this->enable_agency_about_us = get_post_meta($this->post_ID, 'enable_agency_about_us', true);

      //make sure that landing on an agency-specific about us page redirects you back to about-us
      if (!$this->enable_agency_about_us && Taggr::get_current() != 'about-us') {
        header("Location: " . Taggr::get_permalink('about-us'));
      }

      $this->view('layouts/default', $this->get_data());
    }
  }

  private function get_data() {
    $title = get_the_title();
    $title = preg_replace('/ – /', '', $title);

    return array(
      'page' => 'pages/about_us/main',
      'template_class' => 'about-us',
      'cache_timeout' => 60 * 15, /* 15 minutes */
      'page_data' => array(
        'enable_agency_about_us' => $this->enable_agency_about_us ? 1 : 0,
        'title' => $title,
        'excerpt' => $this->post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'children_data' => $this->get_children_data()
      )
    );
  }

  private function get_children_data() {
    $options = [
      'page_id' => $this->post_ID,
      'depth' => 2
    ];
    $response = $this->model->page_tree->get_children($options);
    $children = $response['children'];

    return $children;
  }
}
