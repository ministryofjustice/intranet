<?php

/*
* Single blog post
*/

if (!defined('ABSPATH')) {
    die();
}

get_header();

?>
  <div id="maincontent" class="u-wrapper l-main t-blog-article" role="main">
    <?php get_template_part('src/components/c-breadcrumbs/view'); ?>
    <?php get_template_part('src/components/c-article/view'); ?>
  </div>

<?php get_footer(); ?>
