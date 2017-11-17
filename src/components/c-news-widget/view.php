<?php $newspage_link = 129; ?>
<section class="c-news-widget">
  <h1 class="o-title o-title--section">Featured</h1>
  <?php get_component('c-featured-news-list'); ?>
  <h1 class="o-title o-title--section">News</h1>
  <?php get_component('c-news-list'); ?>
  <a href="<?php the_permalink( $newspage_link ) ?>" class="o-see-all-link">See all news</a>
</section>
