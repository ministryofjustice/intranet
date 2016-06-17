<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-list-widget news-widget">
  <div class="grid">
    <div class="col-lg-6 col-md-12 col-sm-12">
      <ul class="news-list "></ul>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
      <ul class="news-list "></ul>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
      <p class="no-news-message">
        No news found
      </p>
      <p class="see-all-container">
        <a href="<?=get_permalink(Taggr::get_id('news-landing'))?>">See all news</a>
      </p>
    </div>
  </div>

  <?php $this->view('widgets/news_list/news_item') ?>
</div>
