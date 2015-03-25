<?php if (!defined('ABSPATH')) die(); ?>

<div class="my-moj">
  <div class="my-moj-top">
    <div data-department="select-department" class="department-dropdown-box">
      <select class="department" title="Select your intranet">
        <option data-department="select-department" value="">Choose your intranet</option>
        <?php foreach($departments as $department): ?>
          <option data-url="<?=$department['url']?>" data-department="<?=$department['name']?>"><?=$department['label']?></option>
        <?php endforeach ?>
      </select>
      <!--<span class="help-icon"></span>-->
    </div>
  </div>
  <div class="my-moj-body">
    <div class="apps-container">
      <h3 class="category-name">My MOJ</h3>
      <ul class="apps-list">
        <?php foreach($apps as $app): ?>
          <?php $this->view('pages/homepage/my_moj/app_item', $app) ?>
        <?php endforeach ?>
      </ul>
    </div>

    <nav class="quick-links-container">
      <h3 class="category-name">Quick links</h3>
      <?php dynamic_sidebar('my-moj-quick-links'); ?>
      <!--
      <ul class="quick-links-list">
        <?php /*foreach($quick_links as $quick_link): ?>
          <?php $this->view('pages/homepage/my_moj/quick_link_item', $quick_link) ?>
        <?php endforeach*/ ?>
      </ul>
      -->
    </nav>
  </div>
</div>
