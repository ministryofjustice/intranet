<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-list-widget news-widget">
  <ul class="news-list grid"></ul>

  <p class="see-all-container">
    <a href="<?=$see_all_news_url?>">See upcoming news</a>
  </p>

  <?php $this->view('widgets/news_list/news_item') ?>
</div>
