<?php if (!defined('ABSPATH')) die(); ?>

<div class="posts-widget">
  <h2 class="category-name">News</h2>
  <div id="content">
    <article class="c-article-item js-article-item">

      <?php get_news_api(); ?>
    </article>
  </div>
</div>
