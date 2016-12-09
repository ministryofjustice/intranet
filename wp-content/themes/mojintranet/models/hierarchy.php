<?php if (!defined('ABSPATH')) die();

class Hierarchy_model extends MVC_model {
  /** retrieves IDs of all ancestors
   * @param {Integer} $post_id Post ID
   * @return {Array} a list of ancestor IDs sorted hierarchically. The last element is the post ID itself.
   */
  function get_ancestor_ids($post_id) {
    global $MVC;

    wp_reset_query();

    $id = $post_id;
    $type = get_post_type($id);
    $ancestor_ids = get_post_ancestors($id);

    $ancestor_ids = array_reverse($ancestor_ids);
    array_push($ancestor_ids, $id);

    if ($type == 'news') {
      array_unshift($ancestor_ids, Taggr::get_id('news-landing'));
    }
    elseif ($type == 'post') {
      array_unshift($ancestor_ids, Taggr::get_id('blog-landing'));
    }
    elseif ($type == 'event') {
      $regions = get_the_terms($MVC->post_id, 'region');

      if (is_array($regions)) {
        $ancestor_ids = $this->_add_regional_ancestor_ids('page_regional_events', $ancestor_ids);
      }
      else {
        array_unshift($ancestor_ids, Taggr::get_id('events-landing'));
      }
    }
    elseif ($type == 'webchat') {
      array_unshift($ancestor_ids, Taggr::get_id('webchats-landing'));
    }
    elseif ($type == 'regional_page') {
      array_unshift($ancestor_ids, Taggr::get_id('regions-landing'));
    }
    elseif ($type == 'regional_news') {
      $ancestor_ids = $this->_add_regional_ancestor_ids('page_regional_news', $ancestor_ids);
    }

    return $ancestor_ids;
  }

  function get_top_ancestor_id($post_id) {
    $ancestor_ids = $this->get_ancestor_ids($post_id);
    return $ancestor_ids[0];
  }

  private function _add_regional_ancestor_ids($template, $ancestor_ids) {
    $regional_updates_landing_id = $this->_get_posts_by_regional_template($template . '.php')[0]->ID;
    $regional_landing_id = wp_get_post_parent_id($regional_updates_landing_id);

    array_unshift($ancestor_ids, $regional_updates_landing_id);
    array_unshift($ancestor_ids, $regional_landing_id);
    array_unshift($ancestor_ids, Taggr::get_id('regions-landing'));

    return $ancestor_ids;
  }

  private function _get_posts_by_regional_template($regional_template) {
    $region = get_the_terms(get_the_ID(), 'region')[0]->slug;

    //find regional updates page ID
    $args = [
      'post_type' => 'regional_page',
      'meta_query' => [
        [
          'key' => 'dw_regional_template',
          'value' => $regional_template
        ]
      ],
      'tax_query' => [
        'relation' => 'AND',
        [
          'taxonomy' => 'region',
          'field'    => 'slug',
          'terms'    => [$region],
        ]
      ]
    ];

    $query = new WP_Query($args);

    return $query->posts;
  }
}
