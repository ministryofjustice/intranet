<?php if (!defined('ABSPATH')) die(); ?>

<div class="my-moj">
  <div class="apps-container mobile-collapsed">
    <h2 class="category-name">My MoJ</h2>
    <ul class="apps-list">
      <?php foreach($apps as $app): ?>
        <?php $this->view('pages/homepage/my_moj/app_item', $app) ?>
      <?php endforeach ?>
    </ul>
  </div>

  <nav class="quick-links-container mobile-collapsed">
    <h2 class="category-name">Quick links</h2>
    <div class="quick-links-list-container">
      <?php dynamic_sidebar('my-moj-quick-links'); ?>
    </div>
  </nav>
</div>
