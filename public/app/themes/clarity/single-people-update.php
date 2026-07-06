<?php

/**
 * Single `people-update` - for preview only
 */

defined('ABSPATH') || die();

// Redirect to home, if this page is hit, and it's not a preview.
if(!is_preview()) {
  wp_safe_redirect('/');
}

get_header();
?>

<main role="main" id="maincontent" class="u-wrapper l-main">
 
  <?php get_template_part('src/components/c-breadcrumbs/view'); ?>

  <h1 class="o-title o-title--page">Editor preview</h1>

  <?php get_template_part('src/components/c-people-update-article-item/view'); ?>

</main>

<?php get_footer();
