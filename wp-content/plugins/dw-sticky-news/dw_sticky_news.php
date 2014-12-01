<?php
/*
Plugin Name: DW Sticky news
Description: Display sticky news
*/

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

if (class_exists('mmvc')) {
  class DW_sticky_news_controller extends MVC_controller {
    function __construct($args, $instance){
      $this->args = $args;
      $this->instance = $instance;

      parent::__construct();
    }

    function main(){
      $title = apply_filters('widget_title', $this->instance['title']);
      $items = intval($this->instance['items']);

      $cquery = array(
        'orderby'=>'post_date',
        'order'=>'DESC',
        'post_type'=>'news',
        'posts_per_page'=>$items,
        'meta_key'=>'news_listing_type',
        'meta_value'=>'1'
      );

      $news = new WP_Query($cquery);

      $widget_data = array(
        'title' => $title,
        'before_widget' => $args['before_widget'],
        'after_widget' => $args['after_widget'],
        'items' => array()
      );

      $news_count = 0;

      while($news->have_posts()){
        $news->the_post();

        $news_count++;

        if($news_count > 5){
          break;
        }

        $newspod = new Pod('news', $post->ID);

        $widget_data['items'][] = array(
          'offset' => $news_count,
          'id' => $post->ID,
          'url' => get_permalink($ID),
          'title' => get_the_title($post->ID),
          'date' => date("j M Y",strtotime(get_the_date())),
          'excerpt' => get_the_excerpt()
        );
      }

      $this->view('main', $widget_data);

      wp_reset_query();
    }
  }

  class DW_sticky_news extends WP_Widget{
    function DW_sticky_news(){
      parent::WP_Widget(false, 'DW Stcky news', array('description' => 'Sticky news widget'));
    }

    function widget($args, $instance){
      new DW_sticky_news_controller($args, $instance);
    }

    function update($new_instance, $old_instance){
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['items'] = strip_tags($new_instance['items']);

      return $instance;
    }

    function form($instance) {
      $title = esc_attr($instance['title']);
      $items = esc_attr($instance['items']);
      ?>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

        <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
      </p>
      <?php
    }
  }

  add_action('widgets_init', create_function('', 'return register_widget("DW_sticky_news");'));
}

?>