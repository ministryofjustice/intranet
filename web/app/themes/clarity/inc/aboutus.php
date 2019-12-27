<?php
function get_aboutus_list( $page_id ) {
	while ( have_posts() ) :
		the_post();
		$parent_args = array(
			'post_type'      => 'page',
			'post_parent'    => $page_id,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'posts_per_page' => -1,
		);
		$posts       = get_posts( $parent_args );
		if ( is_array( $posts ) ) {
			echo '<div class="l-full-page">';
			foreach ( $posts as $key => $post ) {
				echo '<section class="c-headed-link-list"><h1><a href="' . get_permalink( $post->ID ) . '"><strong>' . $post->post_title . '</strong></a></h1>';
				$child_args  = array(
					'post_type'      => 'page',
					'post_parent'    => $post->ID,
					'order'          => 'ASC',
					'orderby'        => 'menu_order',
					'posts_per_page' => -1,
				);
				$child_posts = get_posts( $child_args );
				if ( ! empty( $child_posts ) ) {
						echo '<ul>';
					foreach ( $child_posts as $key => $child_post ) {
						echo '<li><a href="' . get_permalink( $child_post->ID ) . '">' . $child_post->post_title . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</section>';
			}
			echo '</div>';
		}

  endwhile;
}
