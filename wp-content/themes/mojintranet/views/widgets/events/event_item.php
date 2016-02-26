<?php if (!defined('ABSPATH')) die(); ?>

<script data-name="widget-event-item" type="text/x-partial-template">
  <li class="results-item col-lg-6 col-md-12 col-sm-12">
    <div class="item-row">
      <time class="date-box" datetime="">
        <span class="day-of-week"><?=$event['day_of_week']?></span>
        <span class="day-of-month"><?=$event['day_of_month']?></span>
        <span class="month-year"><?=$event['month_year']?></span>
      </time>
      <div class="content">
        <h3 class="title">
          <a class="results-link" href="<?=$event['url']?>">
            <?=$event['title']?>
          </a>
        </h3>
        <div class="meta">
          <ul>
            <li class="meta-date">
              <span class="label">Date:</span><span class="value"></span>
            </li>
            <li class="meta-time">
              <span class="label">Time:</span><span class="value"></span>
            </li>
            <li class="meta-location">
              <span class="label">Location:</span><span class="value"></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </li>
</script>
