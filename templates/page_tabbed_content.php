<?php
/*
* Clarity template Tabbed content
*/
?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-tabbed-content">
    <h1 class="o-title o-title--page">Page title above tabs</h1>
    <p>Paragraph above tabs.</p>
    <div class="l-secondary">
      <?php get_component('c-left-hand-menu'); ?>
    </div>
    <div class="l-primary js-tabbed-content-container" role="main">
      <?php get_component('c-tabbed-content', 'Management'); ?>
      <?php get_component('c-tabbed-content', 'Staff'); ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>
