<?php

/*
* Single event post
*/
// Query DB to tell this event is in the regions and serve correct breadcrumb
$post_id   = get_the_ID();
$region_id = get_the_terms( $post_id, 'region' );

if ( $region_id ) :
	foreach ( $region_id as $region ) :
		// Current region, ie Scotland, North West etc
		$current_region = '&region=' . $region->term_id;
endforeach;
endif;

var_dump($region_id);
var_dump($current_region);

get_header();

?>
  <div id="maincontent" class="u-wrapper l-main t-events">
	<?php
	if ( $region_id ) :
		get_template_part( 'src/components/c-breadcrumbs/view', 'region-single' );
	else :
		get_template_part( 'src/components/c-breadcrumbs/view', 'event' );
	endif;
	get_template_part( 'src/components/c-event-article/view' );
	?>

	<section class="l-full-page">
	<?php
	get_template_part( 'src/components/c-last-updated/view' );
	get_template_part( 'src/components/c-share-post/view' );
	?>
	</section>

  </div>
<?php
get_footer();
