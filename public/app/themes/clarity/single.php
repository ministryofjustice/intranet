<?php
/**
 *
 * Default single page
 * Also used as the default template for regions
 */

// Query DB to tell this event is in the regions and serve correct breadcrumb
$post_id   = get_the_ID();
$region_id = get_the_terms($post_id, 'region');

get_header();
?>

  <main role="main" id="maincontent" class="u-wrapper l-main l-reverse-order t-default">

    <?php
    if ($region_id) :
        get_template_part('src/components/c-breadcrumbs/view', 'region');
    else :
          get_template_part('src/components/c-breadcrumbs/view', 'event');
    endif;
    ?>

    <div class="l-secondary">
      <?php get_template_part('src/components/c-left-hand-menu/view'); ?>
    </div>

    <div class="l-primary">
      <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
      <?php get_template_part('src/components/c-rich-text-block/view'); ?>

      <section class="l-full-page">
        <?php
          get_template_part('src/components/c-last-updated/view');
          get_template_part('src/components/c-share-post/view');
        ?>
      </section>
    </div>
  </main>

<?php
get_footer();
