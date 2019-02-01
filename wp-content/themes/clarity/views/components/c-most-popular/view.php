<?php
use MOJ\Intranet\Agency;
$agency = get_intranet_code();

?>

<!-- c-most-popular starts here -->
<section class="c-most-popular">
	<?php
	if ( get_field( $agency . '_most_popular_text_1', 'option' ) ) {
		echo '<h1 class="o-title o-title--subtitle">' . get_field( $agency . '_most_popular_title', 'option' ) . '</h1>';
	}
	?>
  <ul>
	<?php
	for ( $i = 0; $i <= 5; $i++ ) {
		$quickLinks[] = [
			'title' => get_field( $agency . '_most_popular_text_' . $i, 'option' ),
			'url'   => get_field( $agency . '_most_popular_link_' . $i, 'option' ),
		];
		if ( ! empty( $quickLinks[ $i ]['title'] ) ) {
			echo '<li>
          <a class="c-most-popular--link" href="' . $quickLinks[ $i ]['url'] . '">' . $quickLinks[ $i ]['title'] . '</a>
        </li>';
		}
	}
	?>
  </ul>
</section>
<!-- c-most-popular ends here -->
