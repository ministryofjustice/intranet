<?php
/**
 *
 * Template name: Campaign content
 *
 */
get_header();
?>
<?php get_template_part('src/components/c-campaign-colour/view'); ?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-default">
    <?php get_template_part('src/components/c-breadcrumbs/view'); ?>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-left-hand-menu/view'); ?>
    </div>
    <div class="l-primary" role="main">
      <?php get_template_part('src/components/c-campaign-banner/view'); ?>
      <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
      <section class="c-article-excerpt">
        <p><?=$excerpt?></p>
      </section>
      <div class="template-container ">
        <?php get_template_part('src/components/c-rich-text-block/view'); ?>
      </div>
      <section class="l-full-page">
      <?php get_template_part('src/components/c-share-post/view'); ?>
      </section>
    </div>
  </div>

<?php
get_footer();
