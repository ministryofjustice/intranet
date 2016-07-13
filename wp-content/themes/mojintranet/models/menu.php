<?php if (!defined('ABSPATH')) die();

class Menu_model extends MVC_model {
  /**
   * Gets menu items for a specified menu location
   * @param {Array} $params Params
   *    {String} 'location' - menu location name
   *    {Integer} 'depth_limit' = 0 - depth limit
   *    {Integer|Boolean} 'post_id' - subject post ID
   *      use boolean:true to auto-detect based on current post
   *
   * @return {Array} array of menu items
   */
  public function get_menu_items($params = []) {
    wp_reset_query();
    $this->top_level_id = false;
    $location = isset($params['location']) ? $params['location'] : 'hq-guidance-most-visited';
    $depth_limit = isset($params['depth_limit']) ? $params['depth_limit'] : 0;
    $post_id = $params['post_id'];

    $locations = get_nav_menu_locations();
    $menu_items = wp_get_nav_menu_items($locations[$location]);

    if (!$menu_items) {
      $menu_items = [];
    }

    if ($post_id === true) {
      $post_id = get_the_ID();
    }

    if ($post_id) {
      $this->top_level_id = $this->model->hierarchy->get_top_ancestor_id($post_id);
    }

    $organised_menu_items = $this->_build_menu_tree_recursive($menu_items, $depth_limit);

    return [
      'results' => $organised_menu_items,
      'top_level_id' => $this->top_level_id
    ];
  }

  private function _build_menu_tree_recursive($data, $depth_limit = 0, $parent_id = 0, $level = 1) {
    $clean_data = [];

    foreach ($data as $key => $item) {
      if ($item->menu_item_parent == $parent_id) {
        $classes = $item->classes;

        if ($this->top_level_id == $item->object_id) {
          $classes[] = 'current';
        }

        $clean_item = [
          'title' => $item->title,
          'id' => $item->ID,
          'object_id' => (int) $item->object_id,
          'url' => $item->url,
          'classes' => implode($classes, ' '),
          'children' => []
        ];

        if (!$depth_limit || $level < $depth_limit) {
          $clean_item['children'] = $this->_build_menu_tree_recursive($data, $depth_limit, $item->ID, $level + 1);
        }

        $clean_data[] = $clean_item;

        unset($data[$key]);
      }
    }

    return $clean_data;
  }
}
