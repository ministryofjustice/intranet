<?php
// Turned off error reporting for now.
error_reporting(0);

//ToDo: Change name to home.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main">
    <?php get_component('c-full-width-banner'); ?>
    <h1 class="o-title o-title--page">Ministry of Justice HQ</h1>
    <?php get_component('c-home-page-primary'); ?>
    <?php get_component('c-home-page-secondary'); ?>
  </div>
<?php get_component('c-global-footer'); ?>
