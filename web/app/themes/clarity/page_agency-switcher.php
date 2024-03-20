<?php

/**
 * Template name: Agency switcher
 */

get_header();
?>

  <main id="maincontent" class="u-wrapper l-main t-agency-switcher" role="main">
    <h1 class="o-title o-title--page">Choose your agency or body</h1>
    <p>Other agencies and bodies have their own specific intranet content available to view by visiting the links below. HMPPS and YJB links are external intranet websites not managed by this central MoJ intranet.</p>
    <?php get_template_part('src/components/c-intranet-switcher/view'); ?>
  </main>

<?php
get_footer();
