<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="main-heading"><?=$title?></h1>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-6 col-md-6 col-sm-12">
      <h2 class="the-moj-heading">The MoJ</h2>
      <!--<div class="excerpt hq-only">
        <?=$excerpt?>
      </div>-->
      <div class="global-categories-box">
        <ul class="index-list global-categories-list grid">
          <?php foreach($children_data as $category): ?>
            <li class="category-item col-lg-12 col-md-12 col-sm-12">
              <h3 class="category-title">
                <a href="<?=$category['url']?>"><?=$category['title']?></a>
              </h3>
              <ul class="children-list">
                <?php foreach($category['children'] as $child): ?>
                  <li class="child-item">
                    <h4 class="child-title">
                      <a href="<?=$child['url']?>"><?=$child['title']?></a>
                    </h4>
                  </li>
                <?php endforeach ?>
              </ul>
            </li>
          <?php endforeach ?>
        </ul>
      </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">
      <h2 class="agency-name-heading"></h2>
      <div class="agency-categories-box">
        <ul class="index-list agency-categories-list"></ul>
      </div>
    </div>

    <?php $this->view('pages/about_us/category_item') ?>
    <?php $this->view('pages/about_us/child_item') ?>
  </div>
</div>
