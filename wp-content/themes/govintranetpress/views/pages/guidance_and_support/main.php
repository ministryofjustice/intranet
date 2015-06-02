<?php if (!defined('ABSPATH')) die(); ?>

<div class="guidance-and-support" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1><?=$title?></h1>
      <?php $this->view('modules/search_form') ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="guidance-categories">
        <?php dynamic_sidebar('guidance-index'); ?>
      </div>
    </div>
  </div>
</div>
