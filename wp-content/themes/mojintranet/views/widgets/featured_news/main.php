<?php if (!defined('ABSPATH')) die(); ?>

<div class="featured-news-widget news-widget">
  <h2 class="category-name">News</h2>
  <ul class="news-list grid"></ul>

  <?php $this->view('widgets/featured_news/news_item') ?>
</div>
