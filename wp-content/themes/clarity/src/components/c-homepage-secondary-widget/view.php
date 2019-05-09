<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\Event;

$oAgency = new Agency();
$oEvent  = new Event();

$activeAgency = $oAgency->getCurrentAgency();
$agency       = $activeAgency['shortcode'];

$mostPopularTitle         = get_field( $agency . '_most_popular_text_1', 'option' );
$event                    = $oEvent->get_event_list( 'search' );
$enable_banner_right_side = get_field( $agency . '_enable_banner_right_side', 'option' );


if ( $mostPopularTitle || $event || $enable_banner_right_side == true ) :
	?>
<!-- c-homepage-secondary-widget starts here -->
<section class="c-homepage-secondary-widget">

  <div class="l-secondary">
	<h1 class="o-title o-title--section">More on the intranet</h1>
  </div>

  <div class="l-secondary">
	<?php

	get_template_part( 'src/components/c-most-popular/view' );

	// Some agencies don't want to have events
	if ( $agency !== 'laa' && $agency !== 'hmcts' ) {
		get_template_part( 'src/components/c-event-listing/view' );
	}
	?>
  </div>

  <div class="l-secondary">
	<?php
	get_template_part( 'src/components/c-aside-banner/view' );
	?>
  </div>

</section>
<!-- c-homepage-secondary-widget ends here -->
	<?php
else :
	// There is no content in any of the columns to display so hide section.
	return;
endif;
