<?php

if (! defined('ABSPATH')) {
    die();
}

?>
<!-- c-news-article starts here -->
<article class="c-news-article l-main">

    <h1 class="o-title o-title--headline"><?php echo get_the_title(); ?></h1>
    <?php get_template_part('src/components/c-article-byline/view', 'news'); ?>
    <?php get_template_part('src/components/c-article-featured-image/view', 'news'); ?>
    <?php
    if (has_excerpt()) {
        get_template_part('src/components/c-article-excerpt/view');
    }
    ?>
    <?php get_template_part('src/components/c-rich-text-block/view'); ?>

</article>
<!-- c-news-article ends here -->
