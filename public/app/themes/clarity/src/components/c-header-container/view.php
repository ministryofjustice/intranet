<?php

use MOJ\Intranet\Agency;

$oAgency = new Agency();

// Show a simplified header if the user has not yet chosen an agency
$simpleHeader = !$oAgency->hasAgencyCookie();

?>
<header class="c-header-container<?= $simpleHeader ? " c-header-container--underlined" : ""?>" role="banner">

  <?php
    // Hide the search bar and main nav bar if hideHeader is set, e.g. on first login before the user has chosen an agency
    get_template_part('src/components/c-logo-bar/view');

    if (!$simpleHeader) {
      get_template_part('src/components/c-search-bar/view');
      get_template_part('src/components/c-main-nav-bar/view');
    }
  ?>

</header>
