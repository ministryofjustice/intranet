<?php
/**
*
* This pulls in the components needed for the homepage primary area.
* These componets are filtered on and off depending on agency via $activeAgency
* This ensures we can have a custom homepage.
*
*/
use MOJ\Intranet\Agency;

$oAgency      = new Agency();
// $activeAgency returns an array of agency properties. This array can be found in /inc/
$activeAgency = $oAgency->getCurrentAgency();
$agency       = $activeAgency['shortcode'];

?>
<!-- c-home-page-primary starts here -->
<section class="c-home-page-primary l-primary" role="main">

  <?php get_component('c-news-widget'); ?>

  <?php
  // removes sliding Need to Know gallery from agency homepage.
   if ($agency !== 'opg' && $agency !== 'pb') {
       get_component('c-need-to-know-widget');
   }
   ?>
  <?php
  // removes event listing from agency homepage.
   if ($agency !== 'laa' && $agency !== 'hmcts') {
       get_component('c-events-widget');
   }
   ?>
</section>
<!-- c-home-page-primary ends here -->
