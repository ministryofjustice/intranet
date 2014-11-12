<?php
/* Template name: Guidance & Support */

get_header();

function get_children_from_API($id){
  $results = new children_request($id);
  return htmlspecialchars(json_encode($results->results_array));
}

$levels = array();
$post_id = get_the_id();
$ids = get_post_ancestors($post_id);
$ids = array_reverse($ids);
array_push($ids, $post_id);

//get JSON data
foreach($ids as $key=>$id){
  $levels[$key] = get_children_from_API($ids[$key]);
}

//get the slug of the top page - we need this for deep-linking (JS)
$top_level_post = get_post($ids[0]);
$top_slug = htmlspecialchars($top_level_post->post_name);

?>

<?php include('mvc/views/shared/breadcrumbs.php'); ?>

<div class="guidance-and-support" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <ul class="tabbed-filter">
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

<?php
get_footer();
