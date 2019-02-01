<?php
use MOJ\Intranet\Agency;
$agency = get_intranet_code();
?>

<section class="c-quick-links">
	<?php
	if ( get_field( $agency . '_quick_links_menu_title_1', 'option' ) ) {
		echo '<h1 class="o-title o-title--section">Quick Links</h1>';
	}
	?>
  <ul>
	<?php
	for ( $i = 0; $i <= 20; $i++ ) {
		$quickLinks[] = array(
			'title' => get_field( $agency . '_quick_links_menu_title_' . $i, 'option' ),
			'url'   => get_field( $agency . '_quick_links_menu_link_' . $i, 'option' ),
		);
		if ( ! empty( $quickLinks[ $i ]['title'] ) ) {
			echo '<li><a href="' . $quickLinks[ $i ]['url'] . '">' . $quickLinks[ $i ]['title'] . '</a></li>';
		}
	}
	?>
  </ul>
</section>
