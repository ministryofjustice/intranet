<?php
$q = $_GET['s'];

//display random search nudge
$randex = '';
$placeholder = get_option('general_intranet_search_placeholder'); //get search placeholder text and variations
if ($placeholder!=''){
	$placeholder = explode( ",", $placeholder );
	srand();
	$randdo = rand(1,5);//1 in 5 chance of showing a variation
	$randpl = rand(2,count($placeholder))-1;//choose a random variation
	if ($randdo==1 && $randpl > 1) {
		$randex=trim($placeholder[$randpl]);
	} else {
		$randex=trim($placeholder[0]);
	}
} else {
	$randex = "Search";
}
?>

<form class="form-horizontal" role="form" id="searchform" name="searchform" action="<?=site_url()?>/search-results/" method="post">
  <div class="row">
    <div class="input-group">
      <div class="search-filter-container">
        <select name="search-filter" class="search-filter">
          <option selected>All</option>
          <option>News</option>
          <option>Pages</option>
          <option>Posts</option>
          <option>Docs</option>
        </select>
      </div>
      <input type="text" class="form-control" placeholder="<?php echo $randex ;?>" name="s" id="s" value="<?php echo the_search_query();?>">
      <button class="search-btn cta" type="submit"></button>
    </div><!-- /input-group -->
  </div>
</form>
