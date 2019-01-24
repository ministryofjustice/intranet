<?php
/*
*  Agency switcher
*/
get_header();
?>

  <div id="maincontent" class="u-wrapper l-main t-agency-switcher" role="navigation">
    <h1 class="o-title o-title--page">Choose your agency or body</h1>

    <?php get_template_part( 'src/components/c-intranet-switcher/view' ); ?>
  </div>

<?php
get_footer();
