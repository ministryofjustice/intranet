<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-list-widget news-widget">
  <div class="grid">
    <ul class="news-list col-lg-6 col-md-12 col-sm-12"></ul>
    <ul class="news-list col-lg-6 col-md-12 col-sm-12"></ul>
  </div>

  <p class="see-all-container">
    <a href="<?=$see_all_news_url?>">See upcoming news</a>
  </p>

  <?php $this->view('widgets/news_list/news_item') ?>
</div>
