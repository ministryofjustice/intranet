<?php
use MOJ\Intranet\Agency;
$agency = get_intranet_code();

?>

<?php

if ( get_field( $agency . '_enable_beta_message', 'option' ) == true ) :
	?>

  <!-- c-phase-banner starts here -->
  <section class="u-wrapper c-phase-banner">
	  <a class="c-phase-banner__icon c-phase-banner__icon--beta" href="/what-beta-means/">Beta</a>
	  <p class="c-phase-banner__message">
	  <?php the_field( $agency . '_beta_banner_text', 'option' ); ?>
	  </p>
  </section>
  <!-- c-phase-banner ends here -->
	<?php
  endif;
