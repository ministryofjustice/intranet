<?php if (!defined('ABSPATH')) die();

class Page_tree_model extends MVC_model {
  private $post_types = array('page', 'webchat');

  /** Get a list of children
   * @param {Array} $options Options //TODO: document the options
   * @return {Array} Children data
   */
  public function get_children($options = []) {
    $options = $this->_normalise_options($options);

    $data = [
      'title' => (string) get_the_title($options['page_id']),
      'id' => (int) $options['page_id'],
      'url' => (string) get_permalink($options['page_id']),
      'children' => []
    ];

    $data['children'] = $this->_get_children_recursive($options);

    return $data;
  }

  /** Get a list of children
   * @param {Array} $options Options //TODO: document the options
   * @return {Array} Children data
   */
  public function get_ancestors($options = []) {
    $options = $this->_normalise_options($options);
    $options['depth'] = 1;

    $ancestors = [];

    do {
      array_push($ancestors, $this->get_children($options));
    }
    while($options['page_id'] = wp_get_post_parent_id($options['page_id']));

    return array_reverse($ancestors);
  }

  private function _normalise_options($options) {
    $options['agency'] = $options['agency'] ?: 'hq';
    $options['additional_filters'] = $options['additional_filters'] ?: '';
    $options['page_id'] = $options['page_id'] ?: 0;
    $options['depth'] = $options['depth'] ?: 1;
    $options['order'] = $options['order'] ?: 'asc';

    return $options;
  }

  /** Get a raw list of children
   * @return {Object} The raw WP Query results object
   */
  private function _get_children_recursive($options, $level = 1) {
    $data = [];

    $children_args = [
      'post_parent' => $options['page_id'],
      'post_type' => $this->post_types,
      'posts_per_page' => -1,
      'tax_query' => $options['tax_query']
    ];

    if(!$options['page_id']) {
      $children_args['meta_key'] = 'is_top_level';
      $children_args['meta_value'] = 1;
    }

    $children = new WP_Query($children_args);

    foreach($children->posts as $post) {
      $row = $this->_format_row($post);

      $new_options = $options;
      $new_options['page_id'] = $row['id'];
      $row['children'] = [];

      if($level < $options['depth']) {
        $row['children'] = $this->_get_children_recursive($new_options, $level + 1);
      }

      $data[] = $row;
    }

    usort($data, [$this, 'sort_children']);

    if($order == 'desc') {
      $data = array_reverse($data);
    }

    return $data;
  }

  /** Format a single results row
   * @param {Object} $post Post object
   * @return {Array} Formatted and trimmed post
   */
  private function _format_row($post) {
    $id = $post->ID;
    setup_postdata(get_post($id));

    $grandchildren = new WP_Query([
      'post_type' => $this->post_types,
      'post_parent' => $id,
      'posts_per_page' => -1
    ]);

    return array(
      'id' => $id,
      'title' => $this->trim_title(get_the_title($id)),
      'url' => get_the_permalink($id),
      'slug' => $post->post_name,
      'excerpt' => get_the_excerpt_by_id($id),
      'order' => $post->menu_order,
      'child_count' => $grandchildren->post_count,
      'is_external' => (boolean) get_post_meta($id, 'redirect_enabled', true),
      'status' => $post->post_status
    );
  }

  /** Trim the title to only contain the part after the first colon character
   * @param {String} $title Subject title
   * @retrun {String} Trimmed title
   */
  private function trim_title($title) {
    return preg_replace('/(.*:\s*)/', "", $title);
  }

  /** A comparator for sorting children by their menu order first, then by title (natural order)
   */
  private function sort_children($a, $b) {
    if($a['menu_order'] > 0 && $b['menu_order'] > 0) {
      return $a['menu_order'] > $b['menu_order'] ? 1 : -1;
    }
    elseif($a['menu_order'] != $b['menu_order']) {
      return $a['menu_order'] > $b['menu_order'] ? -1 : 1;
    }

    return strnatcmp(strtolower($a['title']), strtolower($b['title']));
  }
}
