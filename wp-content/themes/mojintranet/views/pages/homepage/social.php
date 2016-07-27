<?php if (!defined('ABSPATH')) die(); ?>

<div class="social">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h2 class="category-name">Follow us</h2>

      <ul class="social-list"
          data-skeleton-screen-count="2"
          data-skeleton-screen-type="app"></ul>
    </div>
  </div>

  <script data-name="widget-follow-us-item" type="text/x-partial-template">
    <li class="social-item">
      <a class="social-link" target="_blank" rel="external" href="">
        <span class="social-icon"></span>
        <span class="title"></span>
        <span class="sr-only">link opens in a new browser window</span>
      </a>
    </li>
  </script>
</div>
