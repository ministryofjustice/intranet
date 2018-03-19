<?php

/*
* Template Name: Events archive page
*/
if (!defined('ABSPATH')) {
    die();
}

get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

    <div class="l-secondary" role="complementary">
      <?php get_template_part('src/components/c-content-filter/view', 'events'); ?>
    </div>

    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section" id="title-section">Upcoming events</h2>
      <div id="content">
        <?php get_template_part('src/components/c-events-list/view'); ?>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
