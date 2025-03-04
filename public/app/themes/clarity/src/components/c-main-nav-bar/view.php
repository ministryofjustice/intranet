<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\Multisite;

$blog_is_single_agency = Multisite::isSingleAgencyBlog();
// If we are on a multisite blog and it only has one agency, then the menu locations are not prefixed by the agency.
$headermenu = 'header-menu';

if(!$blog_is_single_agency) {
  // Here, we can't get the agency from the multisite, so we are still on blog id 1.
  $oAgency      = new Agency();
  // Get the agency from the cookie.
  $activeAgency = $oAgency->getCurrentAgency();
  // Set a prefix on the menu location.
  $headermenu   = $activeAgency['shortcode'] . '-menu';
}

?>

<nav class="c-main-nav-bar">
  <div class="u-wrapper">
    <?php
    if (has_nav_menu($headermenu)) {
        wp_nav_menu([ 'theme_location' => $headermenu ]);
    } else if ($headermenu !== 'header-menu') {
        wp_nav_menu([ 'theme_location' => 'header-menu' ]);
    }

    ?>
  </div>
</nav>
