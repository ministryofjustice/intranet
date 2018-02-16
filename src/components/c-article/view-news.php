<?php

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-article starts here -->
<article class="c-article">
    <h1 class="o-title o-title--page"><?php echo get_the_title();?></h1>
    <?php get_template_part('src/components/c-article-byline/view','news'); ?>
    <?php get_template_part('src/components/c-rich-text-block/view'); ?>
    <?php get_template_part('src/components/c-share-post/view'); ?>
</article>
<!-- c-article ends here -->
