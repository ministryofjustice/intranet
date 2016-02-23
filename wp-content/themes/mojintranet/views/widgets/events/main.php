<?php if (!defined('ABSPATH')) die(); ?>

<div class="events-widget">
  <h2 class="category-name">Events</h2>
  <ul class="events-list grid"></ul>

  <p class="see-all-container">
    <a href="<?=$see_all_events_url?>">See upcoming events</a>
  </p>

  <?php $this->view('widgets/events/event_item') ?>
</div>
