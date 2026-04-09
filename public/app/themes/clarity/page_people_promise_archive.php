<?php

/**
 * Template name: People Promise archive
 * Template Post Type: page
 */

defined('ABSPATH') || die();

get_header();
?>

<main role="main" id="maincontent" class="u-wrapper l-main">
 
  <?php get_template_part('src/components/c-breadcrumbs/view'); ?>

  <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

  <?php get_template_part('src/components/c-content-filter/view', 'people-promise', ['post_type' => 'page']); ?>

  <?php get_template_part('src/components/c-people-updates/view', 'archive'); ?>

</main>

<?php get_footer();
