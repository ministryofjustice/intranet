<?php

/*
* Homepage news widget
*
*/

$newspage_link = 129;
?>

<section class="c-news-widget">

  <h1 class="o-title o-title--section">Featured</h1>

  <?php get_template_part('src/components/c-featured-news-list/view'); ?>

  <?php get_template_part( 'src/components/c-homepage-video/view' ); ?>
  
  <h1 class="o-title o-title--section">News</h1>

  <?php get_template_part('src/components/c-news-list/view','home'); ?>
  <a href="<?php the_permalink( $newspage_link ) ?>" class="o-see-all-link">See all news</a>

</section>
