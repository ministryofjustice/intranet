<?php
/*
* Clarity template Hub Page
*/
//ToDo: Change name to switcher.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-hub">
    <h1 class="o-title o-title--page">Hub Page</h1>
    <div class="l-secondary">
      <?php get_component('c-left-hand-menu'); ?>
      <?php get_component('c-content-filter'); ?>
    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section">Latest</h2>
      <?php get_component('c-article-list'); ?>
      <h2 class="o-title o-title--section">Events</h2>
      <?php get_component('c-events-list'); ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>
