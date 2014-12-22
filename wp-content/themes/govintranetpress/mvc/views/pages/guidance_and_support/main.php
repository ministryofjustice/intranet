<?php if (!defined('ABSPATH')) die(); ?>

<div class="guidance-and-support" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="tabbed-filters">
        <ul>
          <li class="filter-item selected alpha" data-sort-type="alphabetical">
            <a href="">
              <span class="icon"></span>
              <span class="label">A to Z</span>
            </a>
          </li>
          <li class="filter-item star" data-sort-type="popular">
            <a href="">
              <span class="icon"></span>
              <span class="label">Popular</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="tree-container">
        <div class="tree" data-show-column="">
          <div class="item-container level-1 categories" data-items="<?=$levels[0]?>" data-selected-id="<?=$ids[1]?>">
            <ul class="item-list">
            </ul>
            <a href="#" class="all-categories">See all categories</a>
          </div>
          <div class="item-container level-2 subcategories" data-items="<?=$levels[1]?>" data-selected-id="<?=$ids[2]?>">
            <h2 class="category-name"></h2>
            <div class="list-wrapper">
              <p class="sort-order"></p>
              <ul class="item-list"></ul>
            </div>
          </div>
          <div class="item-container level-3 links" data-items="<?=$levels[2]?>">
            <h2 class="category-name"></h2>
            <div class="list-wrapper">
              <p class="sort-order"></p>
              <ul class="item-list"></ul>
            </div>
          </div>

          <template data-name="guidance-and-support-category-item">
            <li class="item">
              <a>
                <h3 class="title"></h3>
                <p class="description"></p>
              </a>
            </li>
          </template>
        </div>
      </div>
    </div>
  </div>
</div>
