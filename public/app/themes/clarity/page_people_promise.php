<?php

/***
 *
 * Template name: People Promise
 * Template Post Type: page
 */

defined('ABSPATH') || die();

get_header();
?>

<main role="main" id="maincontent" class="u-wrapper l-main">

  <?php get_template_part('src/components/c-breadcrumbs/view'); ?>

  <?php get_template_part('src/components/c-inline-featured-image/view'); ?>

  <?php get_template_part('src/components/c-people-updates/view');  ?>

</main>

<?php
get_footer();
