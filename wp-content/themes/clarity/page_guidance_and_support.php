<?php

/**
 *
 * Template name: Tab template
 * 
 * 
 */
 get_header();
	?>
 <div id="maincontent" class="u-wrapper l-main l-reverse-order t-tabbed-content">
	<?php get_template_part( 'src/components/c-breadcrumbs/view' ); ?>
   <div class="l-secondary">
		<?php get_template_part( 'src/components/c-left-hand-menu/view' ); ?>
   </div>
   <div class="l-primary js-tabbed-content-container" role="main">
	<h1 class="o-title o-title--page"><?php the_title(); ?></h1>
	<div class="c-article-excerpt">
        <p><?php
            if (has_excerpt()) {
                the_excerpt();
            } ?>
        </p>
	</div>

		<?php get_template_part( 'src/components/c-tabbed-nav/view' ); ?>
		<?php get_template_part( 'src/components/c-tabbed-content/view' ); ?>
	 <section class="l-full-page">
		<?php get_template_part( 'src/components/c-last-updated/view' ); ?>
		<?php get_template_part( 'src/components/c-share-post/view' ); ?>
	 </section>
   </div>
</div>
	<?php
	get_footer();
