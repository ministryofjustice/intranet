<?php
/**
 * Template name: CC Campaign Page
 */
?>

<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main t-campaign">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-full-page" role="main">
        <?php get_component('c-full-width-banner'); ?>
        <?php
            while ( have_posts() ) : the_post();
                get_template_part( 'src/components/c-rich-text-block/view' );
            endwhile; // End of the loop.
        ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>