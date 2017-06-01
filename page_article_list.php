<?php
/*
Template Name: Clarity - Article List
*/
//ToDo: Change name to switcher.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main">
    <h1 class="o-title o-title--page">News</h1>
    <div class="l-secondary">
      <?php // get_component('c-left-hand-menu'); ?>
      <?php get_component('c-content-filter'); ?>
    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section">Latest</h2>
      <?php get_component('c-article-list'); ?>
      <?php get_component('c-pagination'); ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>
