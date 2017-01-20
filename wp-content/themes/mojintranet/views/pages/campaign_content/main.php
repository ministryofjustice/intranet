<?php if (!defined('ABSPATH')) die(); ?>

<?php $this->view('pages/campaign_content/dynamic_style', $style_data) ?>

<div class="template-container"
     data-page-id="<?=$id?>">

  <div class="grid content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <?php if(!empty($banner_url)): ?>
        <a href="<?=$banner_url?>">
      <?php endif ?>
          <img src="<?=$banner_image_url?>" class="campaign-banner" />
      <?php if(!empty($banner_url)): ?>
        </a>
      <?php endif ?>
    </div>

    <?php if($lhs_menu_on): ?>
      <div class="col-lg-3 col-md-4 col-sm-12">
        <nav class="menu-list-container">
          <ul class="menu-list"></ul>
        </nav>
      </div>
    <?php endif ?>

    <div class="<?=$content_classes?>">
      <h1 class="page-title"><?=$title?></h1>

      <div class="excerpt">
        <?=$excerpt?>
      </div>

      <div class="editable">
        <?=$content?>
      </div>
    </div>
  </div>

  <?php $this->view('modules/side_navigation') ?>
</div>
