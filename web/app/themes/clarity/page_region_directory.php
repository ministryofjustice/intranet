<?php

/***
 *
 * Template name: Region directory
 *
 */
get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-region-directory">
    <div class="l-full-page" role="main">
        <h1 class="o-title o-title--page"><?php echo get_the_title(); ?></h1>
        <?php get_template_part('src/components/c-rich-text-block/view'); ?>
        <div class="o-headed-separator">
          <div>
            <?php get_template_part('src/components/c-headed-link-list/view', 'region-directory'); ?>
          </div>
        </div>
    </div>
  </div>
<?php
get_footer();
