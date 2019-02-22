<?php
/**
 *
 * Template name: Region archive news
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
	<?php get_template_part( 'src/components/c-breadcrumbs/view', 'region' ); ?>

	<h1 class="o-title o-title--page"><?php the_title(); ?></h1>
	
	<div class="l-secondary" role="complementary" data-termid="<?php echo $region_id; ?>">
		<?php get_template_part( 'src/components/c-content-filter/view', 'region-news' ); ?>
	</div>

	<div class="l-primary" role="main">

	  <div id="content">
		<?php
		  echo '<div id="content">';
		  get_news_api( 'regional_news' );
		  echo '</div>';
		?>
	  </div>
	</div>
  </div>

<?php
get_footer();
