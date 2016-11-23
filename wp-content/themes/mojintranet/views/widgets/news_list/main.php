<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-list-widget news-widget" data-news-type="<?=$type?>">
  <div class="grid">
    <?php for($a = 1; $a <= $number_of_lists; $a++): ?>
      <div class="<?=$list_container_classes?>">
        <ul class="news-list"
            data-skeleton-screen-count="<?=$skeleton_screen_count?>"
            data-skeleton-screen-type="standard"
            ></ul>
      </div>
    <?php endfor ?>

    <div class="col-lg-12 col-md-12 col-sm-12">
      <p class="no-news-message">
        <?=$no_items_found_message?>
      </p>
      <p class="see-all-container">
        <a href="<?=$see_all_url?>"><?=$see_all_label?></a>
      </p>
    </div>
  </div>

  <?php $this->view('widgets/news_list/news_item') ?>
</div>
