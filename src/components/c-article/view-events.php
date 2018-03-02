<?php

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-article events starts here -->
<article class="c-article l-main l-reverse-order">

  <?php // .l-reverse class with l-primary and l-secondary swapped allows for secondary to appear first on mobile ?>
  <aside class="l-secondary">
    <?php get_template_part('src/components/c-events/view'); ?>
  </aside>

  <section class="l-primary" role="main">
    <h1 class="o-title o-title--page"><?php echo get_the_title();?></h1>
    <?php get_template_part('src/components/c-rich-text-block/view'); ?>
  </section>

</article>
<!-- c-article events ends here -->
