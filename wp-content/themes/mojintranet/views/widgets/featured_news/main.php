<?php if (!defined('ABSPATH')) die(); ?>

<div class="featured-news-widget news-widget">
  <h2 class="category-name">News</h2>
  <ul class="news-list grid"
      data-skeleton-screen-count="2"
      data-skeleton-screen-classes="col-lg-6 col-md-6 col-sm-12"
      data-skeleton-screen-type="featured"
      ></ul>

  <?php $this->view('widgets/featured_news/news_item') ?>
</div>
