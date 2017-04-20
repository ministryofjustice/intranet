<?php
// Turned off error reporting for now.
error_reporting(0);

//ToDo: Change name to home.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main">
    <h1 class="o-title o-title--page">Ministry of Justice HQ</h1>
    <div class="l-primary" role="main">
      <?php get_component('c-news-container'); ?>
      <?php get_component('c-need-to-know'); ?>
      <?php get_component('c-events-container'); ?>
    </div>
    <div class="l-secondary" role="complementary">
      <?php get_component('c-my-moj'); ?>
      <?php get_component('c-quick-links'); ?>
      <?php get_component('c-blog-summary'); ?>
      <?php get_component('c-social-links'); ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>
