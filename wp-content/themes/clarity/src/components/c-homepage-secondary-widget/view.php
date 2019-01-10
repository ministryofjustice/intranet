<?php
use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
$agency = $activeAgency['shortcode'];

?>

<!-- c-homepage-secondary-widget starts here -->
<section class="c-homepage-secondary-widget">

  <div class="l-secondary">
    <h1 class="o-title o-title--section">More on the intranet</h1>
  </div>

  <div class="l-secondary">
  <?php
  get_template_part( 'src/components/c-most-popular/view' );
  // removes event listing from agency homepage.
   if ($agency !== 'laa' && $agency !== 'hmcts') {
       get_template_part('src/components/c-event-listing/view');
       echo '<a href="/events/" class="o-see-all-link">See all events</a>';
   }
  ?>
</div>

  <div class="l-secondary">
  <?php
    get_template_part( 'src/components/c-aside-banner/view' );
  ?>
  </div>

</section>
<!-- c-homepage-secondary-widget ends here -->
