<?php if (!defined('ABSPATH')) {
    die();
} ?>
<?php
$terms = get_the_terms($post->ID, 'region');
        foreach ($terms as $term) {
            $region_id = $term->term_id;
        }
?>
<div class="posts-widget">
  <h2 class="category-name">Updates</h2>
  <div id="content">
    <?php get_region_news_api($region_id); ?>
  </div>
</div>
