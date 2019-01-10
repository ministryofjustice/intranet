<?php
use MOJ\Intranet\Agency;
$agency = get_intranet_code();
?>

<!-- c-external-services starts here -->
<section class="c-external-services">
  <ul>
	<?php

	for ( $i = 0; $i <= 10; $i++ ) {

		if ( have_rows( $agency . '_external_services', 'option' ) ) :

			while ( have_rows( $agency . '_external_services', 'option' ) ) :
				the_row();

				$title = get_sub_field( 'external_services_title_' . $i, 'option' );
				$url   = get_sub_field( 'external_services_url_' . $i, 'option' );

				echo '<li><a href="' . esc_url( $url ) . '" class="app-list-link">' . esc_attr( $title ) . '</a></li>';

		  endwhile;

	  endif;

	}

	?>
  </ul>
</section>
<!-- c-external-services ends here -->
