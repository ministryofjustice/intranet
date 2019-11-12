<?php

/***
 *
 * Template name: Guidance and forms
 *
 */
get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-guidance">
    <div role="main">
        <h1 class="o-title o-title--page"><?php echo get_the_title(); ?></h1>
        <?php get_template_part('src/components/c-rich-text-block/view'); ?>
        <div class="o-headed-separator">
          <div>
            <?php get_template_part('src/components/c-headed-link-list/view', 'guidance'); ?>
          </div>
        </div>
    </div>
  </div>
<?php
get_footer();
