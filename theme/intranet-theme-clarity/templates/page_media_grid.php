<?php if (!defined('ABSPATH')) { die(); }

/**
 * Media grid test
 */
get_header();
?>

   <div id="maincontent" class="u-wrapper l-main t-media-grid">

     <?php //get_component('c-breadcrumbs'); ?>

     <h1 class="o-title o-title--page"><?php echo the_title(); ?></h1>

       <?php //get_component('c-full-width-banner'); ?>

      <div class="l-secondary">
        Sidebar.
       <?php //get_component('c-left-hand-menu'); ?>
      </div>

      <div class="l-primary" role="main">
        Primary area.
        <?php //get_component('feature_media'); ?>
        <?php //get_component('lightbox'); ?>
        <?php //get_component('photo_gallery'); ?>
        <?php //get_component('video_gallery'); ?>
        <?php //get_component('quotes'); ?>
      </div>

  <?php //get_component('c-comments');?>

   </div>

 <!-- This footer will be the new footer not finished -->
<?php get_footer(); ?>
