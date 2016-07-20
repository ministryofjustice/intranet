<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-page-id="<?=$id?>">

  <div class="grid content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="">
        <h1 class="page-title"><?=$title?></h1>

        <?php if (!$hide_page_details): ?>
          <ul class="info-list">
            <?php if ($agencies): ?>
              <li>
                <span>Audience:</span>
                <span><?=$agencies?></span>
              </li>
            <?php endif ?>
            <?php /* if ($author): ?>
              <li>
                <span>Content owner:</span>
                <span><?=$author?></span>
              </li>
            <?php endif */ ?>
            <?php if ($last_updated): ?>
              <li>
                <span>Last updated:</span>
                <span><?=$last_updated?></span>
              </li>
            <?php endif ?>
          </ul>
        <?php endif ?>

        <div class="excerpt">
          <?=$excerpt?>
        </div>
        <div class="editable">
          <?=$content?>
        </div>
      </div>
    </div>
  </div>
</div>
