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
    <div class="categories">
      <ul class="level-1">
      </ul>
      <a href="#" class="all-categories">See all categories</a>
    </div>
    <div class="subcategories">
      <h2 class="title"></h2>
      <ul class="level-2">
      </ul>
    </div>
    <div class="links">
      <h2 class="title"></h2>
      <ul class="level-3">
      </ul>
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
