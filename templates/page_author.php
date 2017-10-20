<?php
/*
Template Name: Clarity - Author page
*/
//ToDo: Change name to switcher.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-author-page">
    <h1 class="o-title o-title--page">Blog</h1>
    <?php get_component('c-breadcrumbs'); ?>
    <div class="l-secondary">
      <?php get_component('c-content-filter'); ?>
    </div>
    <div class="l-primary" role="main">
    <?php get_component('c-article-byline'); ?>
      <h2 class="o-title o-title--section">Latest</h2>
      <?php get_component('c-article-list'); ?>
      <?php get_component('c-pagination'); ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>
