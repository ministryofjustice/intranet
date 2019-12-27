<?php
// Display post types beside search result title
$post_id   = get_the_id();
$post_type = get_post_type( $post_id );

// Changes post type name 'post' so the name shows up as a 'blog'
$post_type_blog_filter = str_replace( 'post', 'blog', $post_type );

$terms = get_the_terms( $post_id, 'agency' );
?>
<!-- c-search-result-item starts here -->
<section class="c-search-result-item">
  <h1 class="o-title o-title--subtitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><span class="c-search-result-item__meta__itemtype">| <?php esc_attr_e( ucwords( $post_type_blog_filter ) ); ?></span></h1>
  <div class="c-search-result-item__meta">
	<span class="c-search-result-item__meta__date"><?php echo the_modified_date( 'j F Y' ) . ', '; ?>
	<?php
	if ( isset( $terms ) ) {
		foreach ( $terms as $term ) {
			echo $term->name . ', ';
		}
	};
	?>
	</span>
  </div>
  <div class="c-search-result-item__description">
	<?php the_excerpt(); ?>
  </div>
</section>
<!-- c-search-result-item ends here -->
