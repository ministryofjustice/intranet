<?php if (!defined('ABSPATH')) die();

/* Events widget
 * Requires $results array
 */

?>

<div class="events-widget">
  <h2 class="events-heading">Events</h2>
  <ul class="events-list">
    <?php foreach($results as $event): ?>
      <li class="results-item">
        <div class="item-row">
          <time class="date-box" datetime="">
            <span class="day-of-week"><?=$event['day_of_week']?></span>
            <span class="day-of-month"><?=$event['day_of_month']?></span>
            <span class="month-year"><?=$event['month_year']?></span>
          </time>
          <div class="content">
            <a href="<?=$event['url']?>">
              <h3 class="title"><?=$event['title']?></h3>
            </a>
          </div>
        </div>
      </li>
    <?php endforeach ?>
  </ul>

  <p class="see-all-container">
    <a href="<?=$see_all_url?>">See upcoming events</a>
  </p>
</div>
