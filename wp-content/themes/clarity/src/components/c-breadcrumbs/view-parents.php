<?php

function get_breadcrumb() {

	global $post;

	$trail      = '';
	$page_title = get_the_title( $post->ID );

	if ( $post->post_parent ) {
		$parent_id = $post->post_parent;

		while ( $parent_id ) {
			$page          = get_page( $parent_id );
			$breadcrumbs[] = '<a href="' . get_the_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a> &gt; ';
			$parent_id     = $page->post_parent;
		}

		$breadcrumbs = array_reverse( $breadcrumbs );
		foreach ( $breadcrumbs as $crumb ) {
			$trail .= $crumb;
		}
	}

	$trail .= $page_title;

	return $trail;

}
?>

<section class="c-breadcrumbs">
  <a title="Go to MoJ Intranet." href="<?php echo get_home_url(); ?>" class="home">
	<span>MoJ Intranet</span>
  </a>
  <span> &gt; </span>

	<?php echo get_breadcrumb(); ?>
</section>
