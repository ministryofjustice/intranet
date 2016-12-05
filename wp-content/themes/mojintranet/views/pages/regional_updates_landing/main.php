<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container"
  data-page-id="<?=$id?>"
  data-template-uri="<?=$template_uri?>"
  data-page-base-url="<?=$page_base_url?>"
  data-region="<?=$region?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?=$title?></h1>
      <?=$content?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>

    <div class="col-lg-9 col-md-8 col-sm-12">
      <ul class="results"></ul>

      <?php $this->view('modules/landing_page_pagination') ?>
    </div>
  </div>

  <?php $this->view('modules/news_landing') ?>
  <?php $this->view('modules/side_navigation') ?>
</div>
