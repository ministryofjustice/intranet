<?php if (!defined('ABSPATH')) die(); ?>

<div class="events-widget" data-events-type="<?=$type?>">
  <h2 class="category-name">Events</h2>
  <ul class="events-list grid"
      data-skeleton-screen-count="<?=$skeleton_screen_count?>"
      data-skeleton-screen-type="standard"
      data-skeleton-screen-classes="col-lg-6 col-md-12 col-sm-12"></ul>

  <p class="no-events-message">
    <?=$no_items_found_message?>
  </p>
  <p class="see-all-container">
    <a href="<?=$see_all_url?>"><?=$see_all_label?></a>
  </p>

  <?php $this->view('widgets/events/event_item') ?>
</div>
