<?php
/**
 *
 * Template name: Region archive events
 *
 */
$terms = get_the_terms( get_the_ID(), 'region' );

if ( is_array( $terms ) ) :
	foreach ( $terms as $term ) :
		$region_id = $term->term_id;
  endforeach;
endif;

get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-regional-archive">
	<?php get_template_part( 'src/components/c-breadcrumbs/view', 'region-single' ); ?>

	<h1 class="o-title o-title--page"><?php the_title(); ?></h1>

	<div class="l-secondary" role="complementary" data-termid="<?php echo $region_id; ?>">
		<?php get_template_part( 'src/components/c-content-filter/view', 'region-events' ); ?>
	</div>

	<div class="l-primary" role="main">
	  <div id="content">
		<?php
		  echo '<br><div id="content">';
		  get_events_api( 'region', $region_id );
		  echo '</div>';
		?>
	  </div>
	</div>
  </div>

<?php
get_footer();
