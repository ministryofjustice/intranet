<?php
/* Template name: A-Z */

get_header();

?>

<div class="a-z" data-page-id="<?=get_the_id()?>">
  <ul class="sort">
    <li class="selected" data-sort-type="alphabetical">
      <a href="">
        <span class="icon"></span>
        <span class="label">A - Z</span>
      </a>
    </li>
    <li data-sort-type="popular">
      <a href="">
        <span class="icon"></span>
        <span class="label">Popular</span>
      </a>
    </li>
  </ul>

  <div class="tree">
    <div class="item-container categories">
      <ul class="level-1">
      </ul>
      <a href="#" class="all-categories">See all categories</a>
    </div>
    <div class="item-container subcategories">
      <h2 class="category-name"></h2>
      <div class="list-wrapper">
        <p class="sort-order"></p>
        <ul class="level-2"></ul>
      </div>
    </div>
    <div class="item-container links">
      <h2 class="category-name"></h2>
      <div class="list-wrapper">
        <p class="sort-order"></p>
        <ul class="level-3"></ul>
      </div>
    </div>

    <template data-name="a-z-category-item">
      <li class="item">
        <a>
          <h3 class="title">
          </h3>
          <p class="description"></p>
        </a>
      </li>
    </template>
  </div>
</div>

<?php
get_footer();
