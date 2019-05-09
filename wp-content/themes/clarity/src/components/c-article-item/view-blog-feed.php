<?php

/**
 *  Individual blog feed list item
 *  http://intranet.docker/blogs
 *
 *  @package Clarity
 */
use MOJ\Intranet\Authors;
$oAuthor = new Authors();

$id            = $post['id'];
$authors       = $oAuthor->getAuthorInfo( $id );
$thumbnail     = get_the_post_thumbnail_url( $id, 'user-thumb' );
$thumbnail_alt = get_post_meta( get_post_thumbnail_id( $id ), '_wp_attachment_image_alt', true );

// Filter right-hand blog list so the page your on isn't duplicated and doesn't appear in that list
if ( is_singular( 'post' ) ) {
	if ( $post_id === $post['id'] ) {
		$post['id'] = '';
	}
}

if ( $post['id'] != '' ) :
	?>
<article class="c-article-item js-article-item" >

	<?php
	// Conditional if feature image set or coauthor image set
	if ( $thumbnail ) :
		?>

  <a href="<?php echo esc_url( get_permalink( $id ) ); ?>">
	<img src="<?php echo $thumbnail; ?>" alt="<?php echo $thumbnail_alt; ?>" class="thumbnail">
	</a>

	<?php elseif ( $authors[0]['thumbnail_url'] ) : ?>

  <a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="thumbnail">
	<img src="<?php echo $post['coauthors'][0]['thumbnail_avatar']; ?>" alt="<?php echo $post['coauthors'][0]['display_name']; ?>">
  </a>

	<?php else : ?>

		<?php // If no feature image or guest author image remove photo div and show nothing.
		echo '<!-- No author or blog image provided -->';  ?>

	<?php endif; ?>

	<div class="content">
		
		<h1>
			<a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo $post['title']['rendered']; ?></a>
		</h1>

		<div class="meta">
			<span class="c-article-item__dateline">
			 By <strong><?php echo $post['coauthors'][0]['display_name']; ?></strong> |

			 <?php echo get_gmt_from_date( $post['date'], 'j M Y' ); ?>

			</span> 
		</div>

		<?php
		// On single blog pages where this is listed we don't have room for the excerpt
		if ( ! is_singular( 'post' ) ) :
		?>

		<div class="c-article-excerpt">
			<p><?php echo $post['excerpt']['rendered']; ?></p>
		</div>

		<?php endif; ?>
	</div>

</article>
<?php endif; ?>
