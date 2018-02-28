<?php

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-article events starts here -->
<article class="c-article l-main">
    <div class="l-secondary">
      <?php get_template_part('src/components/c-events/view'); ?>
    </div>

    <div class="l-primary" role="main">
      <h1 class="o-title o-title--page"><?php echo get_the_title();?></h1>
      <?php get_template_part('src/components/c-rich-text-block/view'); ?>
    </div>

</article>
<!-- c-article events ends here -->
