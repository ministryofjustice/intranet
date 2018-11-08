<?php

/***
 *
 * Template name: Team homepage
 * Template Post Type: team_pages
 */

get_header();
?>
	<!-- c-team-homepage ends here -->
	<div id="maincontent" class="u-wrapper l-main t-team-homepage">
	<?php
	  get_template_part( 'src/components/c-breadcrumbs/view' );
	?>
	  <section class="c-full-width-banner">
		<?php get_template_part( 'src/components/c-full-width-banner/view', 'team' ); ?>
	  </section>
	<?php
	  get_template_part( 'src/components/c-team-homepage-primary/view' );
	  get_template_part( 'src/components/c-team-homepage-secondary/view' );
	?>
	</div>
	<!-- c-team-homepage ends here -->
<?php
get_footer();
