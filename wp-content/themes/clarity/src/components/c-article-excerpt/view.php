<?php
$current_template     = get_post_meta( get_the_ID(), '_wp_page_template', true );
$campaign_hub_excerpt = get_field( 'campaign_hub_excerpt' ) ?? '';
?>
<!-- c-article-excerpt starts here -->
<section class="c-article-excerpt">
	<?php

	if ( $current_template === 'page_campaign_landing.php' ) {
		echo stripslashes( wp_filter_post_kses( addslashes( $campaign_hub_excerpt ) ) );
	} else {
		echo the_excerpt();
	}
	?>
</section>
<!-- c-article-excerpt ends here -->
