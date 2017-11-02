<?php if (!defined('ABSPATH')) die();
// This file needs to match the file name exactly it is replacing for the child theme cascade to work correctly. The template name also needs to be exact so that all templates currently in use adopt this new template instead of the old one.
/**
 * Template name: Clarity - Media grid
 */

?>
 <!-- This header will have both the old site's header and new site header and will switch accordingly. Change require_once('base-header.php') in c-global-header  -->
 <?php get_component('c-global-header'); ?>

   <div id="maincontent" class="u-wrapper l-main t-media-grid">
     <h1 class="o-title o-title--page">Media grid title</h1>


       <!-- Media grid uses banner  -->
       <?php get_component('c-full-width-banner'); ?>

       <!-- Using exsiting methods for switching left nav on. Still need to pass $lhs_menu_on data to variable below. Should we really allow for it to be turned off? -->
       <div class="l-secondary">
       <?php get_component('c-left-hand-menu'); ?>
      </div>
      <div class="l-primary" role="main">
        <?php get_component('feature_media'); ?>
        <?php get_component('lightbox'); ?>
        <?php get_component('photo_gallery'); ?>
        <?php get_component('video_gallery'); ?>
        <?php get_component('quotes'); ?>
      </div>

       <?php //get_component('c-comments'); ?>

   </div>

 <!-- This footer will be the new footer not finished -->
 <?php get_component('c-global-footer'); ?>
