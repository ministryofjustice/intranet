<?php
/**
 *
 * Template name: Region landing
 * Template Post Type: regional_page
 *
 */
$terms = get_the_terms(get_the_ID(), 'region');

if (is_array($terms)):
   foreach ($terms as $term):
       $region_id = $term->term_id;
   endforeach;
endif;

get_header();
?>

  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-default">
    <?php get_template_part('src/components/c-breadcrumbs/region','landing'); ?>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-left-hand-menu/view'); ?>
    </div>
    <div class="l-primary" role="main">

      <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

      <?php
        echo '<div id="content">';
        get_news_api('regional_news');
        echo '</div>';

        echo '<br><div id="content">';
        get_events_api('region_landing', $region_id);
        echo '</div>';
      ?>
    </div>
  </div>

<?php
get_footer();
