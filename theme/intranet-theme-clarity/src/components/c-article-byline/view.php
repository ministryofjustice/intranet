<?php

/*
* Single blog byline component
*
*/

use MOJ\Intranet\Authors;

$oAuthor = new Authors();
$id      = get_the_ID();
$authors = $oAuthor->getAuthorInfo( $id );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post(); ?>

<!-- c-article-byline starts here -->
<section class="c-article-byline">

  <?php if ( $authors[0]['thumbnail_url'] ) : ?>

  <img class="c-article-byline__photo" src="<?php echo esc_url( $authors[0]['thumbnail_url'] ); ?>" alt="Photo of author, <?php echo esc_attr( $authors[0]['name'] ); ?>">

  <?php endif; ?>

  <span class="c-article-byline__intro"><?php echo esc_attr( $authors[0]['name'] ); ?></span>
  <span class="c-article-byline__job"><?php echo esc_attr( $authors[0]['job_title'] ); ?></span>
  <span class="c-article-byline__date"><?php the_date( 'd F Y' ); ?></span>

</section>
<!-- c-article-byline ends here -->

		<?php
endwhile;
else :
	esc_html_e( 'Sorry, nothing was found.' );
endif;
