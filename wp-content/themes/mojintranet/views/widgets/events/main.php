<?php if (!defined('ABSPATH')) die(); ?>

<div class="events-widget">
  <h2 class="category-name">Events</h2>
  <ul class="events-list grid"
      data-skeleton-screen-count="2"
      data-skeleton-screen-type="standard"
      data-skeleton-screen-classes="col-lg-6 col-md-12 col-sm-12"></ul>

  <p class="no-events-message">
    No events found
  </p>
  <p class="see-all-container">
    <a href="<?=get_permalink(Taggr::get_id('events-landing'))?>">See all events</a>
  </p>

  <?php $this->view('widgets/events/event_item') ?>
</div>
