<?php if (!defined('ABSPATH')) die(); ?>

<div class="annual-leave">
  <div class="grid">
    <div class="col-lg-8">
      <h2 class="page-category">Guidance and support</h2>
      <h1 class="page-title"><?=$title?></h1>

      <ul class="info-list">
        <li>
          <span>Content owner:</span>
          <span><?=$author?></span>
        </li>
        <li>
          <span>History:</span>
          <span>Published <?=$human_date?></span>
        </li>
      </ul>
      <div class="excerpt">
        Find out about annual leave and public holidays.
      </div>
    </div>

    <div class="col-lg-4">
      <div class="right-hand-menu">
        <h3>Quick links</h3>
        <ul>
          <li>
            <a href="#">Annual leave calculator</a>
          </li>
          <li>
            <a href="#">Application for leave form</a>
          </li>
          <li>
            <a href="#">Annual leave policy</a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3">
      &nbsp;
    </div>
    <div class="col-lg-9">
      <ul class="content-tabs">
        <li data-content="all-staff">
          <a href="">All staff</a>
        </li>
        <li data-content="managers">
          <a href="">Managers</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4">
      <div class="js-floater context-menu" data-floater-limiter-selector=".content-container">
        <h4>Contents</h4>
        <ul class="table-of-contents" data-content-selector=".tab-content">
        </ul>
      </div>
      &nbsp;
    </div>
    <div class="col-lg-9 col-md-8">
      <div class="tab-content editable">
      </div>
    </div>
  </div>

  <div class="template-partial" data-template-type="tab-content" data-content-name="all-staff">
    <h2>What you need to know</h2>

    <p>Your annual leave year begins on your start date. If you're below Senior Civil Servant level, you get:</p>

    <ul>
      <li>25 days when you start (pro-rated for part-time staff)</li>
      <li>30 days after 5 years' service</li>
      <li>8 public holidays</li>
      <li>1 privilege holiday</li>
    </ul>

    <p>Your manager must authorise your leave requests. To buy or sell annual leave, you need to be rated "effective" or better after your probation period (see
    <a href="#">flexible benefits guidance</a>).</p>

    <h2>What you need to do</h2>
    <ul>
      <li>Find out how your team records annual and privilege leave.</li>
      <li>Use the <a href="#">annual leave calculator</a> to work out your allowance, then check it with your manager.</li>
    </ul>

    <h2>Links</h2>
    <ul class="top-links">
      <li><a href="#">annual leave calculator</a></li>
      <li><a href="#">application for leave form</a></li>
    </ul>

    <div class="collapsible-block-container">
      <ul class="collapsible-block">
        <li><a href="#">accrued leave calculator</a></li>
        <li><a href="#">annual leave and sickness absence Q&A</a></li>
        <li><a href="#">annual leave calculation from days to hours</a></li>
        <li><a href="#">annual leave calculation and public and privilege days</a></li>
        <li><a href="#">annual leave policy statement</a></li>
        <li><a href="#">buy and sell annual leave calculator</a></li>
        <li><a href="#">flexible benefits guidance</a></li>
      </ul>

      <a class="collapsible-block-toggle reversed" href="" data-closed-label="More" data-opened-label="Less">More</a>
    </div>
  </div>

  <div class="template-partial" data-template-type="tab-content" data-content-name="managers">
    <h2>What you need to do</h2>
    <p>Work with staff to arrange annual leave, balancing staff and business needs. Let staff take 2 weeks' leave during summer, where possible.</p>
    <p>Be consistent and fair in approving annual leave, and if you can't grant it, explain why. Let staff take annual leave undisturbed, unless it's essential to cancel or postpone it.</p>
    <p>Record leave accurately. Make sure staff take their allowance where possible, and confirm at year end how much is available for the following year.</p>

    <h2>Primary links (in order of importance)</h2>
    <ul class="top-links">
      <li><a href="#">annual and privilege leave managers' guidance</a></li>
    </ul>
  </div>
</div>
