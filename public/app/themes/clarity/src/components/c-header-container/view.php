<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\Multisite;

$blog_is_single_agency = Multisite::isSingleAgencyBlog();

$oAgency = new Agency();

// Show a simplified header if on a multi agency blog, and the user has not yet chosen an agency
$simpleHeader = !$blog_is_single_agency && !$oAgency->hasAgencyCookie();

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
  <!--[if lte IE 9]>
  <div class="u-message u-message--warning">
    You are using an old browser that may impact your web browsing experience. It is recommended you switch to use Firefox or a modern version of Internet Explorer if possible.
  </div>
  <![endif]-->

  <!--[if IE 6]>
  <div class="u-message u-message--error">
    Internet Explorer 6 is not supported and you are likely to experience web page errors. Your web browsing experience will be greatly improved by updating to a modern browser.
  </div>
  <![endif]-->
</header>
