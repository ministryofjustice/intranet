<?php
/**
*
* This pulls in the components needed for the homepage primary area.
* These componets are filtered on and off depending on agency via $activeAgency
* This ensures we can have a custom homepage.
*
*/
use MOJ\Intranet\Agency;

$oAgency = new Agency();
// $activeAgency returns an array of agency properties. This array can be found in /inc/agency.php
$activeAgency = $oAgency->getCurrentAgency();

?>
<!-- c-home-page-primary starts here -->
<section class="c-home-page-primary l-primary" role="main">

  <?php get_component('c-news-widget'); ?>

  <?php
  // Removes sliding need to know gallery from agency homepage.
   if ($activeAgency['shortcode'] !== 'opg' && $activeAgency['shortcode'] !== 'pb') {
       get_component('c-need-to-know-widget');
   }
   ?>
  <?php
  // Removes event listing from agency homepage.
   if ($activeAgency['shortcode'] !== 'laa' && $activeAgency['shortcode'] !== 'hmcts') {
       get_component('c-events-widget');
   }
   ?>
</section>
<!-- c-home-page-primary ends here -->
