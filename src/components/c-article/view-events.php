<?php

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-article events starts here -->
<article class="c-article">
    <h1 class="o-title o-title--page"><?php echo get_the_title();?></h1>
    <?php get_template_part('src/components/c-events/view'); ?>
    <?php get_template_part('src/components/c-rich-text-block/view'); ?>
    <?php get_template_part('src/components/c-share-post/view'); ?>
</article>
<!-- c-article events ends here -->
