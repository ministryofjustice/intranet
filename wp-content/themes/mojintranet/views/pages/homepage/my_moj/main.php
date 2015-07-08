<?php if (!defined('ABSPATH')) die(); ?>

<div class="my-moj">
  <div class="my-moj-top">
    <form class="my-intranet-form">
      <label class="department-dropdown-box">
        <span class="label">My intranet</span>
        <select class="department" title="Select your intranet">
          <option>Please select</option>
          <?php foreach($departments as $department): ?>
            <option data-url="<?=$department['url']?>" data-department="<?=$department['name']?>"><?=$department['label']?></option>
          <?php endforeach ?>
        </select>
      </label>

      <input type="submit" class="visit-cta" value="Visit" />
    </form>
  </div>
  <div class="my-moj-body">
    <div class="apps-container mobile-collapsed">
      <h3 class="category-name">My MoJ</h3>
      <ul class="apps-list">
        <?php foreach($apps as $app): ?>
          <?php $this->view('pages/homepage/my_moj/app_item', $app) ?>
        <?php endforeach ?>
      </ul>
    </div>

    <nav class="quick-links-container mobile-collapsed">
      <h3 class="category-name">Quick links</h3>
      <?php dynamic_sidebar('my-moj-quick-links'); ?>
    </nav>
  </div>
</div>
