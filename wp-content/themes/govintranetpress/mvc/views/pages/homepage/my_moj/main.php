<?php if (!defined('ABSPATH')) die(); ?>

<div class="my-moj">
  <div class="my-moj-top">
    <select class="department">
      <option value="">Choose your department</option>
    </select>
    <span class="help-icon"></span>
  </div>
  <div class="my-moj-body">
    <div class="apps-container">
      <h3>My MoJ</h3>
      <ul class="apps-list">
        <?php foreach($apps as $app): ?>
          <?php $this->view('pages/homepage/my_moj/app_item', $app) ?>
        <?php endforeach ?>
      </ul>
    </div>

    <div class="quick-links-container">
      <h3>Quick links</h3>
      <ul class="quick-links-list">
        <?php foreach($quick_links as $quick_link): ?>
          <?php $this->view('pages/homepage/my_moj/quick_link_item', $quick_link) ?>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</div>
