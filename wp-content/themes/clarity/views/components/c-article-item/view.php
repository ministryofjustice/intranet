<?php

/**
 *
 * Single list item
 **/
global $post;

$id            = $post->ID;
$thumbnail     = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'list-thumbnail' );
$thumbnail_alt = get_post_meta( get_post_thumbnail_id( $id ), '_wp_attachment_image_alt', true );
$thumbnail_url = $thumbnail[0];

?>

<article class="c-article-item js-article-item">

	<?php if ( ! empty( $thumbnail_url ) ) : ?>
	<a href="<?php echo esc_url( get_permalink( $id ) ); ?>">
	  <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $thumbnail_alt; ?>">
	</a>
	<?php endif; ?>

  <div class="text-align">
	<h1><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo get_the_title( $id ); ?></a></h1>

	<div class="meta">
	  <span class="c-article-item__dateline">
		<?php echo get_the_time( 'j M Y', $id ); ?>
	</span>
	</div>
  </div>

</article>
