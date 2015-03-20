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
      <input type="text" class="form-control" placeholder="<?php echo $randex ;?>" name="s" id="s" value="<?=get_query_var('search-string')?>">
      <input class="search-btn cta" type="submit" value="Search" />
    </div>
  </div>
</form>
