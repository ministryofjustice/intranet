<?php if (!defined('ABSPATH')) die(); ?>
<div class="guidance-and-support-content" data-redirect-url="<?=$redirect_url?>" data-redirect-enabled="<?=$redirect_enabled?>">
  <div class="grid">
    <div class="col-lg-8">
      <h2 class="page-category">Guidance</h2>
      <h1 class="page-title"><?=$title?></h1>

      <ul class="info-list">
        <li>
          <span>Content owner:</span>
          <span><a href="mailto:<?=$author_email?>"><?=$author?></a></span>
        </li>
        <li>
          <span>History:</span>
          <span>Updated <?=$human_date?></span>
        </li>
      </ul>
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>

    <div class="col-lg-4">
      <?php if($has_links): ?>
        <div class="right-hand-menu">
          <h3>Quick links</h3>
          <ul>
            <?php foreach($link_array as $link_row): ?>
            <li>
              <a href="<?=$link_row['linkurl']?>"><?=$link_row['linktext']?></a>
            </li>
            <?php endforeach ?>
          </ul>
        </div>
      <?php endif ?>
    </div>
  </div>

  <div class="grid <?=$tab_count <= 1 ? 'hidden' : ''?>">
    <div class="col-lg-3">
      &nbsp;
    </div>
    <div class="col-lg-9">
      <ul class="content-tabs">
        <?php foreach($tab_array as $tab_row): ?>
          <li data-content="<?=$tab_row['name']?>">
            <a href=""><?=$tab_row['title']?></a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4">
      <div class="js-floater context-menu" data-floater-limiter-selector=".content-container">
        <h4>Contents</h4>
        <ul class="table-of-contents" data-content-selector=".tab-content">
        <?php foreach($tab_array as $tab_row): ?>
          <li>
            <a href="#<?=$tab_row['name']?>"><?=$tab_row['title']?></a>
          </li>
        <?php endforeach ?>
        </ul>
      </div>
      &nbsp;
    </div>
    <div class="col-lg-9 col-md-8">
      <div class="tab-content editable">
      </div>
    </div>
  </div>

  <?php foreach($tab_array as $tab_row): ?>
    <div class="template-partial" data-template-type="tab-content" data-content-name="<?=$tab_row['name']?>">
      <?php foreach($tab_row['sections'] as $section): ?>
        <?php if(strlen($section['title'])): ?>
          <h2><?=$section['title']?></h2>
        <?php endif ?>
        <?=wpautop($section['content'])?>
      <?php endforeach ?>
    </div>
  <?php endforeach ?>
</div>
