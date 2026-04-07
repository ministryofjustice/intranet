<?php

/**
 *  Feed for people-updates (People Promise update feed)
 */

defined('ABSPATH') || die();

echo '<div class="c-people-updates">';

// Loop for the last 3 months
for ($i = 0; $i < 3; $i++) {

  // Get Year + Month for each offset month
  $year  = date('Y', strtotime("-$i month"));
  $month = date('m', strtotime("-$i month"));

  // Month name for heading
  $month_name = date('F', strtotime("$year-$month-01"));

  // Query posts for that specific month
  $args = [
    'post_type'      => 'people-update',
    'posts_per_page' => -1,
    'year'           => $year,
    'monthnum'       => $month,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ];

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    echo "<h2 class='o-title o-title--section'>{$month_name} highlights</h2>";
    while ($query->have_posts()) {
      $query->the_post();
      get_template_part('src/components/c-people-update-article-item/view');
    }
  }

  wp_reset_postdata();
}

echo '</div>';
