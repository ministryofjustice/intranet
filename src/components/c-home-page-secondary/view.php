<?php
/**
*
* This pulls in the components needed for the homepage sidebar.
* These componets are filtered on and off depending on agency via $activeAgency
* This ensures we can have a custom homepage sidebar for each agency.
*
*/
use MOJ\Intranet\Agency;
use MOJ\Intranet\HomepageBanners;

$oAgency = new Agency();

// $activeAgency returns an array of agency properties. This array can be found in /inc/agency.php
$activeAgency  = $oAgency->getCurrentAgency();
$agency        = $activeAgency['shortcode'];
$sidebarBanner = HomepageBanners::getSidebarBanner(get_intranet_code());

?>
<!-- c-home-page-secondary starts here -->
<section class="c-home-page-secondary l-secondary" role="complementary">
  <?php get_component('c-my-moj'); ?>
  <?php
    if ($sidebarBanner && $sidebarBanner['visible']) {
        get_component('c-full-width-banner', $sidebarBanner);
    }
    ?>
  <?php
  // Adds my work feed listing from agency homepage.
   if ($agency == 'hmcts') {
       get_component('c-my-work');
   }
   ?>
  <?php get_component('c-quick-links'); ?>
  <?php
  // Removes blog feed listing from agency homepage.
   if ($agency !== 'judicial-office') {
       get_component('c-blog-feed');
   }
  ?>
  <?php
  // Removes social media listing from agency homepage.
   if ($agency !== 'cica') {
       get_component('c-social-links');
   }
  ?>
</section>
<!-- c-home-page-secondary ends here -->
