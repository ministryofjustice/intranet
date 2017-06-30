<?php
/*
Template Name: Clarity - Blog
*/
?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main t-blog-article" role="main">
    <h1 class="o-title o-title--headline">Blog article title</h1>
    <?php get_component('c-article-byline'); ?>
    <?php get_component('c-article-excerpt'); ?>
    <?php get_component('c-rich-text-block'); ?>
    <?php get_component('c-share-post'); ?>
    <?php get_component('c-comment-form'); ?>
    <?php get_component('c-comments'); ?>
  </div>
<?php get_component('c-global-footer'); ?>
