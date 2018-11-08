<?php if (!defined('ABSPATH')) die();

class Content_model extends MVC_model {
  function get_agency() {
    $content_agency = 'shared';

    if(!is_page(['blog','guidance','newspage','events','search-results','about-us']) && !is_front_page() && !is_404()) {
      global $post;
      $content_agency = Agency_Editor::get_post_agency($post->ID);
    }
    return $content_agency;
  }
}
