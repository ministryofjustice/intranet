<?php

/**
 *  Feed for people-updates (People Promise update feed)
 */

defined('ABSPATH') || die();


// Query all posts
$query = new WP_Query([
  'post_type'      => 'people-update',
  'posts_per_page' => -1,
  'orderby'        => 'date',
  'order'          => 'DESC',
]);

?>

<div id="content" class="c-people-updates">

  <?php if ($query->have_posts()) : ?>
    <?php while ($query->have_posts()) : ?>
      <?php $query->the_post(); ?>
      <?php get_template_part('src/components/c-people-update-article-item/view'); ?>
    <?php endwhile; ?>
  <?php endif; ?>

  <?php wp_reset_postdata(); ?>

</div>