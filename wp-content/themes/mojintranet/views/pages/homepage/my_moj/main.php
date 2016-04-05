<?php if (!defined('ABSPATH')) die(); ?>

<div class="my-moj">
  <p class="agency-link-container mobile-collapsed">
    <a></a>
  </p>

  <div class="apps-container mobile-collapsed">
    <h2 class="category-name">My MoJ</h2>
    <ul class="agency-link-list hidden">
      <li class="agency" data-department="opg">
        <a class="agency-link" href="">
          <span class="department-icon-box">
            <span class="department-icon"></span>
          </span>
          <span class="label"></span>
        </a>
      </li>
    </ul>
    <ul class="apps-list">
      <?php foreach($apps as $app): ?>
        <?php $this->view('pages/homepage/my_moj/app_item', $app) ?>
      <?php endforeach ?>
    </ul>
  </div>

  <nav class="quick-links-container mobile-collapsed">
    <h2 class="category-name">Quick links</h2>
    <div class="quick-links-list-container">
      <?php Debug::full($quick_links); ?>
    </div>
  </nav>
</div>
