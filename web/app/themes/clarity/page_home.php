<?php

/**
 *
 * Template name: Homepage
 */

get_header();
?>
    <main role="main" id="maincontent" class="u-wrapper l-main t-home">
    <?php
      get_template_part('src/components/c-agency-wide-banner/view');
      get_template_part('src/components/c-emergency-banner/view');
      get_template_part('src/components/c-homepage-primary/view');
    ?>
    </main>
<?php
get_footer();
