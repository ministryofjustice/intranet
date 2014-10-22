<?php
/* Template name: A-Z */

get_header();

function get_children_from_API($id){
  $results = new children_request($id);
  return htmlspecialchars(json_encode($results->results_array));
}

$post_id = get_the_id();
$levels = array();
$ids = get_post_ancestors($post_id);
$ids = array_reverse($ids);
array_push($ids, $post_id);

foreach($ids as $key=>$id){
  $levels[$key] = get_children_from_API($ids[$key]);
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
