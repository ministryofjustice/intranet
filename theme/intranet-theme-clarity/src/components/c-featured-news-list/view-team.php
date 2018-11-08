<?php

/*
* Team homepage featured news section
*
*/

  $first_featured_item  = get_field( 'first_featured_item' );
  $second_featured_item = get_field( 'second_featured_item' );
  $featuredNews         = [ $first_featured_item, $second_featured_item ];

?>

<!-- c-featured-news-list-view-team starts -->
<section class="c-featured-news-list">

<?php
foreach ( $featuredNews as $post ) :

	include locate_template( 'src/components/c-article-item/view-team-feature.php' );

endforeach;
  wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly
?>

</section>
<!-- c-featured-news-list-view-team starts -->
