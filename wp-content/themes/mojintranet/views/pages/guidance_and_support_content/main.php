<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container" data-page-id="<?=$id?>">

  <div class="grid content-container">
    <?php if($lhs_menu_on): ?>
      <div class="col-lg-3 col-md-4 col-sm-12">
        <nav class="menu-list-container">
          <ul class="menu-list"></ul>
        </nav>
      </div>
    <?php endif ?>

    <div class="col-lg-9 col-md-8 col-sm-12">
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
      </div>

      <?php if (count($tabs) > 1): ?>
        <ul role="tablist" class="content-tabs <?php $tablist_classes?>">
          <?php foreach($tabs as $tab): ?>
            <li role="presentation" class="tab-title" data-tab-name="<?=$tab['name']?>">
              <a id="tab-<?=$tab['name']?>" role="tab" aria-selected="false" aria-controls="panel-<?=$tab['name']?>" href="">
                <?=$tab['tab_title']?>
              </a>
            </li>
          <?php endforeach ?>
        </ul>
      <?php endif ?>

      <?php $this->view('pages/guidance_and_support_content/tabs', ['tabs' => $tabs]) ?>

      <?php $this->view('modules/side_navigation') ?>
    </div>
  </div>
</div>
