<?php

/**
 *
 * Template name: Homepage
 */

get_header();
?>
    <div id="maincontent" class="u-wrapper l-main t-home">
    <?php
      get_template_part('src/components/c-agency-wide-banner/view');
      get_template_part('src/components/c-emergency-banner/view');
      get_template_part('src/components/c-homepage-primary/view');
    ?>
    </div>
<?php
get_footer();
