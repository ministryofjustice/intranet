<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

$oAgency = new Agency();

$activeAgency = $oAgency->getCurrentAgency();
$agency       = $activeAgency['shortcode'];
$agency_id = $activeAgency['wp_tag_id'];

$mostPopularTitle         = get_field($agency . '_most_popular_text_1', 'option');
$EventsHelper  = new EventsHelper();
$events = $EventsHelper->get_events($agency_id);
$enable_banner_right_side = get_field($agency . '_enable_banner_right_side', 'option');


if ($mostPopularTitle || $events || $enable_banner_right_side == true) :
    ?>
<!-- c-homepage-secondary-widget starts here -->
<section class="c-homepage-secondary-widget">

  <div class="l-secondary">
    <h2 class="o-title o-title--section">More on the intranet</h2>
  </div>

  <div class="l-secondary">
    <?php

    get_template_part('src/components/c-most-popular/view');
    include locate_template('src/components/c-event-listing/view.php');

    ?>
  </div>

  <div class="l-secondary">
    <?php
    get_template_part('src/components/c-aside-banner/view');
    ?>
  </div>

</section>
<!-- c-homepage-secondary-widget ends here -->
    <?php
else :
    // There is no content in any of the columns to display so hide section.
    return;
endif;
