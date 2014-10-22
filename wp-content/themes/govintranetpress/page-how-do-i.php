<?php
/* Template name: A-Z */

get_header();

if(!function_exists('get_children_from_API')){
  function get_children_from_API($id){
    $results = new children_request($id);
    return htmlspecialchars(json_encode($results->results_array));
  }

  /*
   * Level0 - top page
   * Level1 - category level
   * Level2 - subcategory level
   * Level3 - link level
   */

  $level0_id = get_the_id();
  $level1_id = 265;
  $level2_id = 275;

  $level1 = get_children_from_API(get_the_id());
  $level2 = get_children_from_API($level1_id);
  $level3 = get_children_from_API($level2_id);
}

?>

<div class="a-z">
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
    <div class="item-container level-1 categories" data-items="<?=$level1?>" data-selected-id="<?=$level1_id?>">
      <ul class="item-list">
      </ul>
      <a href="#" class="all-categories">See all categories</a>
    </div>
    <div class="item-container level-2 subcategories" data-items="<?=$level2?>" data-selected-id="<?=$level2_id?>">
      <h2 class="category-name"></h2>
      <div class="list-wrapper">
        <p class="sort-order"></p>
        <ul class="item-list"></ul>
      </div>
    </div>
    <div class="item-container level-3 links" data-items="<?=$level3?>">
      <h2 class="category-name"></h2>
      <div class="list-wrapper">
        <p class="sort-order"></p>
        <ul class="item-list"></ul>
      </div>
    </div>

    <template data-name="a-z-category-item">
      <li class="item">
        <a>
          <h3 class="title"></h3>
          <p class="description"></p>
        </a>
      </li>
    </template>
  </div>
</div>

<?php
get_footer();
