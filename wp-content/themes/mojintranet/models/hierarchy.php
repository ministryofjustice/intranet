<?php if (!defined('ABSPATH')) die();

class Hierarchy_model extends MVC_model {
  /** retrieves IDs of all ancestors
   */
  function get_ancestor_ids($post_id) {
    wp_reset_query();

    $id = $post_id;
    $type = get_post_type($id);
    $ancestor_ids = get_post_ancestors($id);
    $landing_page_id = null;

    $ancestor_ids = array_reverse($ancestor_ids);
    array_push($ancestor_ids, $id);

    if ($type == 'news') {
      $landing_page_id = Taggr::get_id('news-landing');
    }
    elseif ($type == 'post') {
      $landing_page_id = Taggr::get_id('blog-landing');
    }
    elseif ($type == 'event') {
      $landing_page_id = Taggr::get_id('events-landing');
    }
    elseif ($type == 'webchat') {
      $landing_page_id = Taggr::get_id('webchats-landing');
    }

    if ($landing_page_id) {
      array_unshift($ancestor_ids, $landing_page_id);
    }

    return $ancestor_ids;
  }

  function get_top_ancestor_id($post_id) {
    $ancestor_ids = $this->get_ancestor_ids($post_id);
    return $ancestor_ids[0];
  }
}
