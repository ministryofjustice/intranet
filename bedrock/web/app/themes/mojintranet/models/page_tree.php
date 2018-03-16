<?php if (!defined('ABSPATH')) die();

/** TODO: this model should be updated to make use of hierarchy model.
 * Ideally, this model should also be renamed to Tree_navigation model
 * or something to indicate that it's specifically used for that purpose only.
 */
class Page_tree_model extends MVC_model {
  private $post_types = array('page', 'regional_page', 'webchat');

  /** Get a list of children
   * @param {Array} $options Options (see below)
   * @return {Array} Children data
   *
   * Options array:
   *  {String} agency - agency name
   *  {String} additional_params - list of terms to be used in the following format: taxonomy1=termx|taxonomy2=termy
   *  {String} tag - tag name of the subject post
   *  {String} page_id - the ID of the subject post
   *
   *  Note: Either tag or page_id has to be provided. If both are provided then the tag is used.
   */
  public function get_children($options = []) {
    $options = $this->_normalise_options($options);

    if ($options['tag']) {
      $options['page_id'] = Taggr::get_id($options['tag']);

      if (!$options['page_id']) {
        return [
          'children' => []
        ];
      }
    }

    $data = $this->_format_row(get_post($options['page_id']));

    if (Taggr::get_tag($options['page_id']) == 'regions-landing') {
      $children = get_posts([
        'meta_key' => 'dw_regional_template',
        'meta_value' => 'page_regional_landing.php',
        'post_type' => 'any',
        'posts_per_page' => -1
      ]);

      $data['children'] = [];

      foreach ($children as $child) {
        $data['children'][] = $this->_format_row($child);
      }
    }
    else {
      $data['children'] = $this->_get_children_recursive($options);
    }

    usort($data['children'], [$this, 'sort_children']);

    if($options['order'] == 'desc') {
      $data = array_reverse($data);
    }

    return $data;
  }

  /** Get a list of children
   * @param {Array} $options Options (see below)
   * @return {Array} Children data
   *
   * Options array:
   *  {String} agency - agency name
   *  {String} additional_params - list of terms to be used in the following format: taxonomy1=termx|taxonomy2=termy
   *  {String} page_id - the ID of the subject post
   */
  public function get_ancestors($options = []) {
    $options = $this->_normalise_options($options);
    $options['depth'] = 1;

    $ancestor_ids = $this->model->hierarchy->get_ancestor_ids($options['page_id']);
    $ancestors = [];

    foreach ($ancestor_ids as $ancestor_id) {
      $options['page_id'] = $ancestor_id;

      $ancestors[] = $this->get_children($options);
    }

    return $ancestors;
  }

  public function get_guidance_index($global_options = []) {
    $global_options = $this->_normalise_options($global_options);
    $data = [];

    //1. get top level categories
    $options = $global_options;
    $options['depth'] = 1;
    $options['page_id'] = Taggr::get_id('guidance-index');

    $data['categories'] = $this->_get_children_recursive($options);

    //2. get most visited
    $most_visited = [];

    $menu_items = get_field($global_options['agency'].'_visited_links', 'option');

    if (isset($menu_items)) {
      foreach ($menu_items as $menu_item) {
        $mv_link['title'] = $menu_item['link_title'];
        $mv_link['url'] = $menu_item['link_url'];
        $mv_link['children'] = [];

        if (is_array($menu_item['sub_links']) && count($menu_item['sub_links']) > 0) {
          foreach ($menu_item['sub_links'] as $sublink) {
            $sub_link['title'] = $sublink['sublink_title'];
            $sub_link['url'] = $sublink['sublink_url'];
            $mv_link['children'][] = $sub_link;
          }

        }
        $most_visited[] = $mv_link;
      }
    }

    $data['most_visited'] = $most_visited;

    //3. get guidance bottom
    $data['bottom_pages'] = [];

    $bottom_pages = new WP_Query([
      'post_type' => $this->post_types,
      'posts_per_page' => -1,
      'tax_query' => $global_options['tax_query'],
      'meta_query' => [
        [
          'key' => 'dw_' . $global_options['agency'] .'_guidance_bottom',
          'value' => 1
        ]
      ]
    ]);

    foreach ($bottom_pages->posts as $page) {
      $page = $this->_format_row($page);
      $page['children'] = $this->_get_children_recursive([
        'page_id' => $page['id'],
        'depth' => 2
      ]);

      $data['bottom_pages'][] = $page;
    }

    return $data;
  }

  private function _normalise_options($options) {
    $options['agency'] = get_array_value($options, 'agency', 'hq');
    $options['additional_filters'] = get_array_value($options, 'additional_filters', '');
    $options['page_id'] = get_array_value($options, 'page_id', 0);
    $options['depth'] = get_array_value($options, 'depth', 1);
    $options['order'] = get_array_value($options, 'order', 'asc');
    $options['tax_query'] = get_array_value($options, 'tax_query', []);
    $options['meta_query'] = get_array_value($options, 'meta_query', []);
    $options['tag'] = get_array_value($options, 'tag', '');

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
      'tax_query' => $options['tax_query'],
      'meta_query' => $options['meta_query']
    ];

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

    if($options['order'] == 'desc') {
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
      'status' => $post->post_status,
      'children' => []
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
    if ($a['order'] != $b['order']) {
      if ($a['order'] > 0 && $b['order'] > 0) {
        return $a['order'] > $b['order'] ? 1 : -1;
      }

      return $a['order'] > $b['order'] ? -1 : 1;
    }

    return strnatcmp(strtolower($a['title']), strtolower($b['title']));
  }
}
