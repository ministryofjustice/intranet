<?php

namespace MOJ\Intranet\PeopleUpdate;

use WP_Query;

/**
 * Feed for people-updates (People Promise update feed)
 *
 * Includes:
 * - Article items (the updates)
 * - Link to archive
 */

defined('ABSPATH') || die();

/**
 * Get the query and month name for x months ago.
 */
function get_query_by_months_ago($months_ago)
{
  // Get Year + Month for each offset month
  $year  = date('Y', strtotime("-$months_ago month"));
  $month = date('m', strtotime("-$months_ago month"));

  // Query posts for that specific month
  $query_args = [
    'post_type'      => 'people-update',
    'posts_per_page' => -1,
    'year'           => $year,
    'monthnum'       => $month,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ];

  return [
    new WP_Query($query_args),
    // Month name for heading
    date('F', strtotime("$year-$month-01"))
  ];
}

/**
 * Get the archive ID based on the archive:
 *
 * - being a child page of the highlights page.
 * - having the `page_people_update_archive.php` template.
 */
function get_archive_id($post_id)
{
  $children = get_children([
    'post_parent' => $post_id,
    'post_type'   => 'page',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'fields' => 'ids',
    'meta_query' => [[
      'key'   => '_wp_page_template',
      'value' => 'page_people_update_archive.php'
    ]]
  ]);

  return $children[0] ?? null;
}

?>

<div class="c-people-updates">

  <?php for ($months_ago = 0; $months_ago < 3; $months_ago++) : ?>

    <?php [$query, $month_name] = get_query_by_months_ago($months_ago); ?>

    <?php if ($query->have_posts()) : ?>
      <h2 class='o-title o-title--section'><?= esc_html($month_name) ?> highlights</h2>

      <?php while ($query->have_posts()) : ?>
        <?php $query->the_post(); ?>
        <?php get_template_part('src/components/c-people-update-article-item/view'); ?>
      <?php endwhile; ?>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
  <?php endfor; ?>

  <?php $archive = get_archive_id($post->ID); ?>
  <?php if ($archive) : ?>
    <a class="o-button c-people-updates__archive-button" href="<?= get_permalink($archive) ?>">
      View archived posts
    </a>
  <?php endif; ?>

</div>
