<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\SearchQueryArgs;

/*
* Template Name: Blog archive
*/

defined('ABSPATH') || exit;

get_header();

// Use the SearchQueryProps class to create the query properties.
$query_args = new SearchQueryArgs((new Agency())->getCurrentAgency()['wp_tag_id'], 'post', 1, 10);

// Run the query.
$query = new WP_Query($query_args->get());

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">

    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

    <div class="l-secondary">
      <?php get_template_part('src/components/c-content-filter/view', null, ['post_type' => 'post', 'template' => 'view-news-feed']); ?>
    </div>

    <div class="l-primary">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>


      <div id="content">
        <?php foreach ($query->posts as $key => $post) {
          include locate_template('src/components/c-article-item/view-blog-feed.php');
        } ?>
      </div>

      <?php get_template_part('src/components/c-pagination/view-infinite', null, ['total_pages' => $query->max_num_pages, 'page' => 1]); ?>

    </div>

  </main>

<?php
get_footer();
