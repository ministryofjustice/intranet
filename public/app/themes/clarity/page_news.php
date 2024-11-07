<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\SearchQueryArgs;

/*
* Template Name: News archive
*/

defined('ABSPATH') || exit;

get_header();

// Use the SearchQueryProps class to create the query properties.
$query_args = new SearchQueryArgs((new Agency())->getCurrentAgency()['wp_tag_id'], 'news', 1, 10);

// Run the query.
$query = new WP_Query($query_args->get());

?>

<main role="main" id="maincontent" class="u-wrapper l-main t-article-list">

  <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

  <div class="l-secondary">
    <?php get_template_part('src/components/c-content-filter/view', null, ['post_type' => 'news', 'template' => 'view-news-feed']); ?>
  </div>

  <div role="status" class="l-primary">

    <h2 class="o-title o-title--section" id="title-section">Latest</h2>

    <div id="content">
      <?php
      while ($query->have_posts()): $query->the_post();
        get_template_part('src/components/c-article-item/view-news-feed');
      endwhile;
      ?>
    </div>

    <?php get_template_part('src/components/c-pagination/view-infinite', null, ['total_pages' => $query->max_num_pages, 'page' => 1]); ?>

  </div>
</main>

<?php
get_footer();
