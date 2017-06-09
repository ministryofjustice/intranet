<?php
/*
Template Name: Clarity - News
*/
?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main" role="main">
    <h1><a href="">News article title</a></h1>
    <?php get_component('c-article-byline'); ?>
    <?php get_component('c-article-excerpt'); ?>
    <section class="l-secondary">
      <?php get_component('c-article-featured-image'); ?>
    </section>
    <section class="l-primary">
      <?php get_component('c-rich-text-block'); ?>
    </section>
  </div>
<?php get_component('c-global-footer'); ?>
