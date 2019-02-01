<?php
$post_id   = get_the_id();
$post_type = get_post_type( $post_id );
$terms     = get_the_terms( $post_id, 'agency' );
?>
<!-- c-last-updated starts here -->
<section class="c-last-updated">
  <p><span class="c-share-post__meta__date">Last reviewed: <?php echo the_modified_date( 'j F Y' ); ?></span></p>
  <p><span class="c-share-post__meta__date">Content tagged as:
	<?php
	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			echo ' ' . $term->name . ', ';
		}
	}
	?>
  </span></p>
</section>
<!-- c-last-updated ends here -->
