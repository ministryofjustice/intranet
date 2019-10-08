<?php
/**
 *
 * Template name: Default template
 * Template Post Type: page, regional_page
 */

$post_id   = get_the_ID();
$region_id = get_the_terms( $post_id, 'region' );

get_header();
?>

  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-default">

  <?php
  if (is_singular('regional_page') && $region_id) :
      get_template_part('src/components/c-breadcrumbs/view', 'region-single');
  else :
      get_template_part('src/components/c-breadcrumbs/view');
  endif;
  ?>

	<div class="l-secondary">
		<?php get_template_part( 'src/components/c-left-hand-menu/view' ); ?>
  </div>

	<div class="l-primary" role="main">
	  <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

    <?php
      get_template_part( 'src/components/c-article-excerpt/view' );
      get_template_part( 'src/components/c-rich-text-block/view' );
     ?>

	  <section class="l-full-page">

    <?php
      get_template_part( 'src/components/c-last-updated/view' );
      get_template_part( 'src/components/c-share-post/view' );
    ?>

	  </section>
  </div>

  </div>

<?php
get_footer();
