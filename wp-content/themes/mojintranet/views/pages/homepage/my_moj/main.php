<?php if (!defined('ABSPATH')) die(); ?>

<div class="my-moj">
  <p class="agency-link-container mobile-collapsed">
    <a></a>
  </p>

  <div class="apps-container mobile-collapsed">
    <h2 class="category-name">My <span class="agency-abbreviation"></span></h2>
    <ul class="agency-link-list hidden">
      <li class="agency" data-department="opg">
        <a class="agency-link" href="">
          <span class="label"></span>
        </a>
      </li>
    </ul>
    <ul class="apps-list"
        data-skeleton-screen-count="10"
        data-skeleton-screen-type="app"></ul>
  </div>

  <nav class="quick-links-container mobile-collapsed">
    <h2 class="category-name">Quick links</h2>
    <ul class="quick-links-list"
        data-skeleton-screen-count="10"
        data-skeleton-screen-type="one-liner"></ul>
  </nav>

  <?php $this->view('pages/homepage/my_moj/app_item') ?>
  <?php $this->view('pages/homepage/my_moj/quick_link_item') ?>
</div>
