<?php
/**
 *
 * Default single page
 * Also used as the default template for regions
 */
get_header();
?>

  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-default">
    <?php get_template_part('src/components/c-breadcrumbs/view', 'generic'); ?>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-left-hand-menu/view'); ?>
    </div>
    <div class="l-primary" role="main">
      <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
      <?php get_template_part('src/components/c-rich-text-block/view'); ?>
      <section class="l-full-page">
      <?php get_template_part( 'src/components/c-last-updated/view' ); ?>
      <?php get_template_part('src/components/c-share-post/view'); ?>
      </section>
    </div>
  </div>

<?php
get_footer();
