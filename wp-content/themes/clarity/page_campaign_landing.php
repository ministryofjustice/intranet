<?php
use MOJ\Intranet\Agency;

/*
* Template Name: Campaign hub
*/

if (!defined('ABSPATH')) {
    die();
}

global $post;

$terms = get_the_terms($post->ID, 'campaign_category');
if (isset($terms)) {
    foreach ($terms as $term) {
        $campaign_id = $term->term_id;
    }
}

get_header(); ?>
<div id="maincontent" class="u-wrapper l-main l-reverse-order t-hub">
  <?php get_template_part('src/components/c-breadcrumbs/view'); ?>
  <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
  <div class="l-secondary">
    <?php get_template_part('src/components/c-left-hand-menu/view'); ?>
  </div>
  <div class="l-primary campaign" role="main">
      <?php get_campaign_news_api($campaign_id); ?>
      <?php get_campaign_post_api($campaign_id); ?>
      <?php get_events_api('campaign', $campaign_id); ?>
  </div>
</div>

<?php
get_footer();
