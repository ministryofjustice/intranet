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
        <ul class="categories-list grid">
          <?php foreach($children_data as $category): ?>
            <li class="category-item col-lg-4 col-md-4 col-sm-12">
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
  </div>
</div>
