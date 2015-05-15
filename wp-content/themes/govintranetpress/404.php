<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 */

get_header();

$notfound=get_option('general_intranet_page_not_found');
if (!$notfound) $notfound = "That's an error"; ?>
	<div class='col-lg-12 white'>
		<h1 class="entry-title"><?php echo $notfound; ?></h1>
		<p><?php _e( 'The page that you are trying to reach doesn\'t exist.', 'govintranetpress' ); ?></p><br>
	</div>
<?php get_footer(); ?>