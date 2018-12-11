<?php
use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
$agency = $activeAgency['shortcode'];

?>

<!-- c-popular-content starts here -->
<section class="c-popular-content">

  <div class="l-secondary"><h1 class="o-title o-title--page">More on the intranet</h1></div>
  <div class="l-secondary"><?php get_template_part( 'src/components/c-most-popular/view' ); ?></div>

  <div class="l-secondary">
  <?php
  // removes event listing from agency homepage.
   if ($agency !== 'laa' && $agency !== 'hmcts') {
       get_template_part('src/components/c-events-item/view', 'home');
   }
  ?>
  </div>

</section>
<!-- c-popular-content ends here -->
