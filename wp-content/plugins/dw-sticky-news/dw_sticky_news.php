<?php
/*
Plugin Name: DW Sticky news
Description: Display sticky news
*/

class dwStickyNews extends WP_Widget{
  function dwStickyNews(){
    parent::WP_Widget(false, 'DW Stcky news', array('description' => 'Sticky news widget'));
  }

  function news_item_view($data){
    ?>

    <li data-page="<?=$data['offset']?>">
      <h3 class="item-title">
        <a href="<?=$data['url']?>"><?=$data['title']?></a>
      </h3>
      <p class='news-date-wrapper'>
        <span class='news_date'><?=$data['date']?></span>
      </p>
      <p class="excerpt"><?=$data['excerpt']?></p>
    </li>

    <?php
  }

  function widget_view($data){
    ?>

    <div id="need-to-know">
      <?=$data['before_widget']?>
      <h3 class="widget-title"><?=$data['title']?></h3>
      <div class="need-to-know-inner">
        <ul class="need-to-know-list">
          <?php foreach($data['items'] as $index=>$item): ?>
            <?php $this->news_item_view($item) ?>
          <?php endforeach ?>
        </ul>
        <ul class="page-list">
          <?php for($a=1, $count=count($data['items']); $a<=$count; $a++): ?>
            <li class="item" data-page-id="<?=$a?>">
              <?=$a?>
            </li>
          <?php endfor ?>
        </ul>
      </div>
      <?=$data['after_widget']?>
    </div>

    <?php
  }

  function widget($args, $instance){
    extract($args);
    $title = apply_filters('widget_title', $instance['title']);
    $items = intval($instance['items']);

    $containerclasses = $instance['containerclasses'];

    $cquery = array(
      'orderby'=>'post_date',
      'order'=>'DESC',
      'post_type'=>'news',
      'posts_per_page'=>$items,
      'meta_key'=>'news_listing_type',
      'meta_value'=>'1'
    );

    $news = new WP_Query($cquery);

    $widget_data = array();
    $widget_data['title'] = $title;
    $widget_data['before_widget'] = $before_widget;
    $widget_data['after_widget'] = $after_widget;
    $widget_data['items'] = array();

    $k = 0;

    while($news->have_posts()){
      $news->the_post();
      $k++;
      if($k > 5){
        break;
      }

      $newspod = new Pod('news', $post->ID);

      $widget_data['items'][] = array(
        'offset' => $k,
        'id' => $post->ID,
        'url' => get_permalink($ID),
        'title' => get_the_title($post->ID),
        'date' => date("j M Y",strtotime(get_the_date())),
        'excerpt' => get_the_excerpt()
      );
    }

    $this->widget_view($widget_data);

    wp_reset_query();
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

add_action('widgets_init', create_function('', 'return register_widget("dwStickyNews");'));

?>
