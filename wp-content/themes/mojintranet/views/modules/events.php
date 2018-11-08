<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-partial" data-name="events-item">
  <li class="results-item">
    <div class="item-row">
      <time class="date-box" datetime="">
        <span class="day-of-week"></span>
        <span class="day-of-month"></span>
        <span class="month-year"></span>
      </time>
      <div class="content">
        <h3 class="title">
          <a href="" class="results-link"></a>
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
    <span class="ie-clear"></span>
  </li>
</div>

<div class="template-partial" data-name="events-results-page-title">
  <h2 class="results-page-title results-title">Latest</h2>
</div>

<div class="template-partial" data-name="events-filtered-results-title">
  <h2 class="filtered-results-title results-title">
    <span class="results-count"></span>
    <span class="results-count-description"></span>
    <span class="containing">containing</span>
    <span class="keywords"></span>
    <span class="for-date">for</span>
    <span class="date"></span>
  </h2>
</div>
