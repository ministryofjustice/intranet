<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\SearchQueryArgs;

/*
* Template Name: News archive
*/

defined('ABSPATH') || exit;

get_header();

// Use the SearchQueryProps class to create the query properties.
$query_args = new SearchQueryArgs((new Agency())->getCurrentAgency()['wp_tag_id'], 'news', 1, 6, true);

// Run the query.
$query = new WP_Query($query_args->get());

?>

<!-- c-news-article starts here -->
<article class="c-news-article l-main">

  <section class="l-primary">
    <h1 class="o-title o-title--headline"><?= get_the_title() ?></h1>
    <?php
    get_template_part('src/components/c-article-byline/view', 'news');
    get_template_part('src/components/c-article-featured-image/view', 'news');
    get_template_part('src/components/c-article-excerpt/view');
    get_template_part('src/components/c-rich-text-block/view');
    ?>

  </section>

  <aside class="l-secondary">

    <?php
        echo '<h1 class="o-title">Recent news</h1>';
        foreach ($query->posts as $key => $post) {
          include locate_template('src/components/c-article-item/view-news-feed.php');
        }
    ?>

  </aside>

</article>
<!-- c-news-article ends here -->
