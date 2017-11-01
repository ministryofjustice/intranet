<?php
/**
 * Template name: CC Campaign Page
 */
?>
<?php
    $campaign_colour = get_field('dw_campaign_colour');
?>
<style>
.page-template-page_campaign #maincontent .c-rich-text-block h2,
.page-template-page_campaign #maincontent .c-rich-text-block h3,
.page-template-page_campaign #maincontent .c-rich-text-block h4,
.page-template-page_campaign #maincontent .c-rich-text-block h5,
.page-template-page_campaign #maincontent .c-rich-text-block h6 {
  color: <?=$campaign_colour?>;
}

.page-template-page_campaign #maincontent .c-rich-text-block hr {
  display: inline-block;
  width: 100%;
  margin: 10px 0 0;
  border: 1px solid <?=$campaign_colour?>;
}

.main-content .c-rich-text-block .example {
  border-left-color: <?=$campaign_colour?>;
}
</style>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main t-campaign">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-full-page" role="main">
        <?php get_component('c-full-width-banner'); ?>
        <?php
            while ( have_posts() ) : the_post();
                get_template_part( 'src/components/c-rich-text-block/view' );
            endwhile; // End of the loop.
        ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>