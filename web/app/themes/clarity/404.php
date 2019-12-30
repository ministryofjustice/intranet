<?php

/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Clarity
 * @since Clarity 1.0
 */

get_header();
?>

    <div id="maincontent" class="u-wrapper l-main t-page-error">
      <section class="l-primary" role="main">
        <?php get_template_part('src/components/c-page-error/view'); ?>
    </section>
    
    </div>
<?php
get_footer();
