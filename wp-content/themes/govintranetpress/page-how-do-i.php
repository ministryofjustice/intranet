<?php
/* Template name: A-Z */

get_header();

?>

<div class="a-z" data-page-id="<?=get_the_id()?>">
  <ul class="sort">
    <li data-sort-type="alphabetical">
      <a href="">
        <span class="icon"></span>
        <span class="label">A - Z</span>
      </a>
    </li>
    <li class="selected" data-sort-type="popular">
      <a href="">
        <span class="icon"></span>
        <span class="label">Popular</span>
      </a>
    </li>
  </ul>

  <div class="tree">
    <ul class="categories level-1">
    </ul>
    <ul class="subcategories level-2">
    </ul>
    <ul class="links level-3">
    </ul>

    <template data-name="a-z-category-item">
      <li>
        <h3 class="title">
          <a></a>
        </h3>
        <p class="description"></p>
      </li>
    </template>
  </div>
</div>

<?php
get_footer();
