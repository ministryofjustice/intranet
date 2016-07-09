<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1><?=$title?></h1>
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="guidance-categories">
        <div class="categories-box">
          <div class="most-visited">
            <h2 class="category">Most visited</h2>
            <ul class="index-list guidance-categories-list large"></ul>
          </div>

          <div class="a-to-z">
            <h2 class="category">All</h2>
            <ul class="index-list guidance-categories-list small"></ul>
          </div>

          <div class="embedded">
            <ul class="index-list guidance-categories-list"></ul>
          </div>
        </div>

        <?php $this->view('pages/guidance_and_support/category_item') ?>
        <?php $this->view('pages/guidance_and_support/child_item') ?>
      </div>
    </div>
  </div>
</div>
