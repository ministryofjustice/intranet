<?php
/*
Plugin Name: DW Guidance index
Description: Display guidance index page menu
*/

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

if (class_exists('mmvc')) {
  class DW_guidance_index_controller extends MVC_controller {
    function __construct($args, $instance) {
      $this->args = $args;
      $this->instance = $instance;

      parent::__construct();
    }

    function main() {
      $this->view('main', $this->get_data());
    }

    private function get_data() {
      return array(
        'menu_data' => $this->get_menu_data()
      );
    }

    private function get_menu_data() {
      $menu_id = $this->instance['nav_menu'];
      $menu = wp_get_nav_menu_items($menu_id);

      $current_menu = array();
      $large_menu = array();
      $small_menu = array();
      $count = 0;

      foreach($menu as $item) {
        $count++;

        $item = array(
          'title' => $item->title,
          'ID' => $item->ID,
          'menu_item_parent' => $item->menu_item_parent,
          'url' => $item->url,
          'children' => array()
        );

        if($item['menu_item_parent']) {
          $current_menu[$item['menu_item_parent']]['children'][$item['ID']] = $item;
        }
        else {
          $current_menu[$item['ID']] = $item;
          //$current_menu[$item['ID']]['type'] = $count > 9 ? 'small' : 'large';
        }
      }

      $large_menu = array_splice($current_menu, 0, 6);

      return array(
        'large_menu' => $large_menu,
        'small_menu' => $current_menu
      );
    }
  }

  class DW_guidance_index extends WP_Widget {
    function __construct() {
      parent::WP_Widget(false, 'Guidance index', array('description' => 'Guidance index widget'));
    }

    function widget($args, $instance) {
      new DW_guidance_index_controller($args, $instance);
    }

    function update($new_instance, $old_instance){
      $instance = array();
      $instance['nav_menu'] = (!empty($new_instance['nav_menu'])) ? strip_tags($new_instance['nav_menu']) : '';

      return $instance;
    }

    function form($instance) {
  		$nav_menu = isset($instance['nav_menu']) ? $instance['nav_menu'] : __('menu_id', 'text_domain');
  		$menus = get_terms('nav_menu', array('hide_empty' => false));
      ?>

      <p>
        <label for="<?=$this->get_field_id('nav_menu')?>"><?php _e('Select Menu:')?></label>
        <select id="<?=$this->get_field_id('nav_menu')?>" name="<?=$this->get_field_name('nav_menu')?>">
        <?php foreach($menus as $menu): ?>
            <option value="<?=$menu->term_id?>" <?=selected( $nav_menu, $menu->term_id, false)?> ><?=$menu->name?></option>
        <?php endforeach ?>
        </select>
      </p>

      <?php
    }
  }

  add_action('widgets_init', create_function('', 'return register_widget("DW_guidance_index");'));
}

?>
