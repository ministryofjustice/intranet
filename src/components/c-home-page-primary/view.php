<?php
use MOJ\Intranet\Agency;

/**
*
* This pulls in the components needed for the homepage primary area.
* These componets are filtered on and off depending on agency via $activeAgency
* This ensures we can have a custom homepage.
*
*/
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
$agency = $activeAgency['shortcode'];

?>
<!-- c-home-page-primary starts here -->
<section class="c-home-page-primary l-primary" role="main">

  <?php get_template_part('src/components/c-news-widget/view'); ?>

  <?php
  // removes sliding Need to Know gallery from agency homepage.
   if ($agency !== 'opg' && $agency !== 'pb' && $agency !== 'cica' && $agency !== 'judicial-office') {
       get_template_part('src/components/c-need-to-know-widget/view');
   }
   ?>
  <?php
  // removes event listing from agency homepage.
   if ($agency !== 'laa' && $agency !== 'hmcts') {
       get_template_part('src/components/c-events-widget/view');
   }
   ?>
</section>
<!-- c-home-page-primary ends here -->
