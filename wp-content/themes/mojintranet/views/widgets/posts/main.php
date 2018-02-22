<?php if (!defined('ABSPATH')) {
    die();
} ?>
<?php

  global $post;

  $terms = get_the_terms($post->ID, 'campaign_category');

  foreach ($terms as $term) {
      $campaign_id = $term->term_id;
  }
?>
<div class="posts-widget">
  <h2 class="category-name">Blogs</h2>
  <div id="content">
    <?php get_campaign_post_api($campaign_id); ?>
  </div>
</div>
