<?php if (!defined('ABSPATH')) die();

/* Events widget
 * Requires $results array
 */

?>

<div class="events-widget">
  <h2 class="events-heading">Events</h2>
  <ul class="events-list">
    <?php foreach($events as $event): ?>
      <li class="results-item">
        <div class="item-row">
          <time class="date-box" datetime="">
            <span class="day-of-week"><?=$event['day_of_week']?></span>
            <span class="day-of-month"><?=$event['day_of_month']?></span>
            <span class="month-year"><?=$event['month_year']?></span>
          </time>
          <div class="content">
            <h3 class="title">
              <a href="<?=$event['url']?>">
                <?=$event['title']?>
              </a>
            </h3>
            <div class="meta">
              <ul>
                <?php if($event['multiday']): ?>
                  <li class="meta-date">
                    <span class="label">Date:</span><span class="value"><?=$event['date']?></span>
                  </li>
                <?php else: ?>
                  <li class="meta-time">
                    <span class="label">Time:</span><span class="value"><?=$event['time']?></span>
                  </li>
                <?php endif ?>

                <?php if($event['location']): ?>
                  <li class="meta-location">
                    <span class="label">Location:</span><span class="value"><?=$event['location']?></span>
                  </li>
                <?php endif ?>
              </ul>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach ?>
  </ul>

  <p class="see-all-container">
    <a href="<?=$see_all_events_url?>">See upcoming events</a>
  </p>
</div>
