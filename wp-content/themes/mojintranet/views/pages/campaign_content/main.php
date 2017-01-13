<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-page-id="<?=$id?>">

  <div class="grid content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <a href="<?=$banner_url?>">
        <img src="<?=$banner_image_url?>" class="campaign-banner" />
      </a>
    </div>
    <?php if($thumbnail): ?>
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="campaign-banner">
          <img src="<?=$thumbnail?>" class="img img-responsive" alt="<?=$thumbnail_alt_text?>" />
        </div>
      </div>
    <?php endif ?>

    <?php if($lhs_menu_on): ?>
      <div class="col-lg-3 col-md-4 col-sm-12">
        <nav class="menu-list-container">
          <ul class="menu-list"></ul>
        </nav>
      </div>
    <?php endif ?>

    <div class="<?=$content_classes?>">
      <div class="">

        <h1 class="page-title"><?=$title?></h1>

        <div class="editable">
          <?=var_dump($colour_hex)?>
          <?=$content?>

        </div>
      </div>
    </div>
  </div>

  <div class="template-partial" data-name="menu-item">
    <li class="menu-item">
      <div class="menu-item-container">
        <a href="" class="menu-item-link"></a>
        <button href="#" class="dropdown-button">Expand</button>
      </div>
      <ul class="children-list">
      </ul>
    </li>
  </div>

  <div class="template-partial" data-name="child-item">
    <li class="child-item">
      <a href="" class="child-item-link"></a>
    </li>
  </div>

</div>
