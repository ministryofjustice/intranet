<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;
use MOJ\Intranet\Multisite;

$blog_is_single_agency = Multisite::isSingleAgencyBlog();
// If we are on a multisite blog and it only has one agency, then the field prefix is empty.
$field_prefix = '';

if (!$blog_is_single_agency) {
  // Here, we can't get the agency from the multisite, so we are still on blog id 1. 
  // Get the agency from the cookie.
  $oAgency = new Agency();

  $activeAgency = $oAgency->getCurrentAgency();
  $agency       = $activeAgency['shortcode'];
  $agency_id = $activeAgency['wp_tag_id'];

  // Set the field prefix to the agency shortcode.
  $field_prefix = $agency . '_';

  $EventsHelper  = new EventsHelper();
  $events = $EventsHelper->get_events($agency_id);
}

// Get the fields (with or without the agency context in the field name).
$mostPopularTitle         = get_field($field_prefix . 'most_popular_text_1', 'option');
$enable_banner_right_side = get_field($field_prefix . 'enable_banner_right_side', 'option');


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
      // TODO - replace all `include locate_template` with `get_template_part`
      // this will help make sure global variables are not missed during a multisite refactor.
      include locate_template('src/components/c-event-listing/view.php');

      if($blog_is_single_agency) {
        echo '<h2 class="o-title o-title--subtitle">Multisite events placeholder</h2>';
      }

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
