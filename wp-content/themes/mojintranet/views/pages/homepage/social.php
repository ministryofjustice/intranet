<?php if (!defined('ABSPATH')) die(); ?>

<div class="social">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h2 class="category-name">Follow us</h2>

      <?php Debug::full($this->model->follow_us->get_data()); ?>

      <ul class="social-list"></ul>
        <!--<li class="social-item twitter">
          <a class="social-link" target="_blank" rel="external" href="https://twitter.com/MoJGovUK">
            <span class="social-icon"></span>
            MoJ on Twitter
            <span class="sr-only">(link opens in a new browser window)</span>
          </a>
        </li>
        <li class="social-item yammer">
          <a class="social-link" target="_blank" rel="external" href="https://www.yammer.com/justice.gsi.gov.uk/dialog/authenticate">
            <span class="social-icon"></span>
            MoJ on Yammer
            <span class="sr-only">(link opens in a new browser window)</span>
          </a>
        </li>-->
    </div>
  </div>
</div>
