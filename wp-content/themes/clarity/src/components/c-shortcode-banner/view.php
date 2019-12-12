<?php
/**
 * Shortcode banners
 * This supports a number of banners that display in the post body content.
 * The type of banner that displays depends on the variable passed in the shortcode.
 * For example [text-banner <variable here>]
 * $atts, is the shortcode array of possible variables.
 * See inc/shortcodes.php
 */

 // Shortcode banner following 2019 election, [text-banner publishedpre2019]
if ( in_array( 'publishedpre2019', $atts ) ) {
	$bannerText = 'This was published under the 2010 to 2019 government';
} else {
	// hide banner if for some reason there is no variable passed
	$bannerText = '';
	echo '<style> .c-shortcode-banner { display:none !important }</style>';
	trigger_error( 'User has not provided the shortcode variable on this post ( file: /c-shortcode-banner.php).', E_USER_NOTICE );
}
?>

<!-- c-shortcode-banner starts here -->
<section class="c-shortcode-banner" aria-label="Notice">
	<p class="c-shortcode-banner__text"><?php _e( $bannerText ); ?></p>
</section>
<!-- c-shortcode-banner ends here -->
