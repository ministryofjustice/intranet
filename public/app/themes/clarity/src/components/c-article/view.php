<?php

/*
* Genertic blog body
*
*/


use MOJ\Intranet\Agency;
use MOJ\Intranet\SearchQueryArgs;

/*
* Template Name: Blog archive
*/

if (!defined('ABSPATH')) {
    die();
}

defined('ABSPATH') || exit;

get_header();

// Use the SearchQueryProps class to create the query properties.
$query_args = new SearchQueryArgs((new Agency())->getCurrentAgency()['wp_tag_id'], 'post', 1, 5, true);

// Run the query.
$recent_query = new WP_Query($query_args->get());

?>
<!-- c-article starts here -->
<article class="c-article">

    <h1 class="o-title o-title--page"><?= get_the_title() ?></h1>

    <div class="l-primary">
        <?php
        get_template_part('src/components/c-article-byline/view');
        get_template_part('src/components/c-rich-text-block/view');
        ?>

    </div>

    <aside class="l-secondary">
        <h2 class="o-title">Recent blog posts</h2>
        <?php
        while ($recent_query->have_posts()): $recent_query->the_post();
            get_template_part('src/components/c-article-item/view-blog-feed', null, ['show_excerpt' => false]);
        endwhile;
        wp_reset_postdata();
        ?>
    </aside>

</article>
<!-- c-article ends here -->