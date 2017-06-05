<?php
// Turned off error reporting for now.
error_reporting(0);

use MOJ\Intranet\Agency;
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();



//ToDo: Change name to home.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main">
    <?php get_component('c-emergency-banner'); ?>
    <h1 class="o-title o-title--page"><?php echo $activeAgency['label'];?></h1>
    <?php get_component('c-home-page-primary'); ?>
    <?php get_component('c-home-page-secondary'); ?>
  </div>
<?php get_component('c-global-footer'); ?>
