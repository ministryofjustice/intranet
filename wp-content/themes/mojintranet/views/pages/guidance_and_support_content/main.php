<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-redirect-url="<?=$redirect_url?>"
     data-redirect-enabled="<?=$redirect_enabled?>"
     data-is-imported="<?=$is_imported?>"
     data-page-id="<?=$id?>"
     data-children-data="<?=$children_data?>">

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
      <?php if($is_imported||true): ?>
        <?php $this->view('modules/imported_banner'); ?>
      <?php endif ?>

      <div class="">
        <h2 class="page-category"><?=$page_category ?></h2>
        <h1 class="page-title"><?=$title?></h1>
        <div class="excerpt">
          <?=$excerpt?>
        </div>
      </div>

      <ul class="content-tabs <?=$tab_count >= 3 ? 'small-tabs' : ''?> <?=$tab_count <= 1 ? 'hidden' : ''?>">
        <?php foreach($tab_array as $tab_row): ?>
          <li data-content="<?=$tab_row['name']?>">
            <a href=""><?=$tab_row['title']?></a>
          </li>
        <?php endforeach ?>
      </ul>

      <div class="tab-content editable"></div>
      <span class="date-updated">Last updated: <?=$human_date?></span>
    </div>
  </div>

  <?php $tab_no=1; ?>
  <?php foreach($tab_array as $tab_number=>$tab_row): ?>
    <div class="template-partial" data-template-type="tab-content" data-content-name="<?=$tab_row['name']?>">
      <?php foreach($tab_row['sections'] as $section): ?>
        <h2><?=$section['title']?></h2>
        <?=$section['content']?>
      <?php endforeach ?>

      <?php if(count($link_array->tabs[$tab_number])): ?>
        <?php if($autoheadings): ?>
          <h2><?=$links_title?></h2>
        <?php endif ?>
        <ul>
          <?php foreach($link_array->tabs[$tab_number] as $link_row): ?>
            <?php if($link_row['heading']): ?>
              </ul>
              <h2><?=$link_row['linktext']?></h2>
              <ul>
            <?php else: ?>
              <li>
                <a href="<?=$link_row['linkurl']?>"><?=$link_row['linktext']?></a>
              </li>
            <?php endif ?>
          <?php endforeach ?>
        </ul>
        </div>
      <?php endif ?>
    </div>
    <?php $tab_no++; ?>
  <?php endforeach ?>

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
