<?php

/*
* Genertic blog body
*
*/

?>
<!-- c-article starts here -->
<article class="c-article">

	<h1 class="o-title o-title--page"><?php echo get_the_title(); ?></h1>
  
	<?php get_template_part( 'src/components/c-article-byline/view' ); ?>
	<?php get_template_part( 'src/components/c-rich-text-block/view' ); ?>

</article>
<!-- c-article ends here -->
