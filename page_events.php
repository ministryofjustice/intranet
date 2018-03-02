<?php

/*
* Template Name: Events archive page
*/
if (!defined('ABSPATH')) {
    die();
}

get_header();

?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-content-filter/view', 'events'); ?>
    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section" id="title-section">Upcoming Events</h2>
      <div id="content">
        <?php get_events_api(); ?>
      </div>
      <?php //get_pagination( 'event' ); ?>
    </div>
  </div>

<?php get_footer(); ?>
