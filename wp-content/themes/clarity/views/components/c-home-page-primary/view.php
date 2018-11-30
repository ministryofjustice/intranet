<?php
use MOJ\Intranet\Agency;

// Exit if accessed directly
if (! defined('ABSPATH')) {
    die();
}
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
<section class="c-home-page-primary l-full-page" role="main">

  <?php get_template_part('src/components/c-news-widget/view'); ?>

  <?php
  // removes event listing from agency homepage.
   if ($agency !== 'laa' && $agency !== 'hmcts') {
       get_template_part('src/components/c-events-widget/view');
   }
   ?>
</section>
<!-- c-home-page-primary ends here -->
