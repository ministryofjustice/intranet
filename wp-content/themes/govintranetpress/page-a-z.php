<?php
/* Template name: A-Z */

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

<div class="row">
  <div class='breadcrumbs'>
    <?php if(function_exists('bcn_display') && !is_front_page()) {
      bcn_display();
    }?>
  </div>
</div>

<div class="a-z" data-top-level-slug="<?=$top_slug?>">
  <ul class="tabbed-filter">
    <li class="selected" data-sort-type="alphabetical">
      <a href="">
        <span class="icon"></span>
        <span class="label">Content</span>
      </a>
    </li>
    <li data-sort-type="popular">
      <a href="">
        <span class="icon"></span>
        <span class="label">Forms &amp; templates</span>
      </a>
    </li>
  </ul>
</div>

<?php
get_footer();
