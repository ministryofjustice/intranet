<?php if (!defined('ABSPATH')) {
    die();
} ?>
<?php
$id = get_the_ID();
$terms = get_the_terms($id, 'region');

if (is_array($terms)){
  foreach ($terms as $term) {
      $region_id = $term->term_id;
  }
}

?>
<div class="posts-widget">
  <h2 class="category-name">News</h2>
  <div id="content">
    <?php get_region_news_api($region_id); ?>
  </div>
</div>
