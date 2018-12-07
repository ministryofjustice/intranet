<?php

/**

 *
 *
 * Template name: Homepage
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

get_header();
?>
	<div id="maincontent" class="u-wrapper l-main t-home">
	<?php
		get_template_part( 'src/components/c-emergency-banner/view' );
	  get_template_part( 'src/components/c-home-page-primary/view' );
	?>
	</div>
<?php get_footer(); ?>
