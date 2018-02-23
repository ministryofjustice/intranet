<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="title"><?=$title?></h1>
    </div>
    <div class="col-lg-8 col-md-10 col-sm-12">
      <div class="results-item">
        <div class="item-row">
          <time class="date-box" datetime="">
            <span class="day-of-week"><?=$day_of_week?></span>
            <span class="day-of-month"><?=$day_of_month?></span>
            <span class="month-year"><?=$month_year?></span>
          </time>
          <div class="content">
            <div class="meta">
              <ul>
                <?php if($multiday): ?>
                  <li class="meta-date">
                    <span class="label">Date:</span><span class="value"><?=$date?></span>
                  </li>
                <?php else: ?>
                  <li class="meta-time">
                    <span class="label">Time:</span><span class="value"><?=$time?></span>
                  </li>
                <?php endif ?>

                <?php if($location): ?>
                  <li class="meta-location">
                    <span class="label">Location:</span><span class="value"><?=$location?></span>
                  </li>
                <?php endif ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="item-row content editable">
          <?=$content?>
        </div>
        <div class="item-row">
          <span class="share-via-email-icon"></span>
          <a class="share-via-email"
             href="mailto:"
             data-title="<?=htmlspecialchars($title)?>"
             data-date="<?=htmlspecialchars($human_date)?>"
             data-body="<?=htmlspecialchars($share_email_body)?>">Share event by email</a>
        </div>
      </div>
    </div>
  </div>
</div>
