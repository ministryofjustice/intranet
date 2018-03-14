<?php

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-event-article starts here -->
<article class="c-article l-main l-reverse-order">

  <?php // Using .l-reverse-order, l-primary and l-secondary classes in this way allows columns to swap on mobile ?>
  <aside class="l-secondary" role="complementary">
    <?php get_template_part('src/components/c-events-item/view'); ?>
  </aside>

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <section class="l-primary" role="main">
    <h1 class="o-title o-title--page"><?php echo get_the_title(); ?></h1>
  <?php endwhile; else : ?>
    <p><?php esc_html_e('Calendar event details'); ?></p>
  <?php endif; ?>

    <?php get_template_part('src/components/c-rich-text-block/view'); ?>
  </section>

</article>
<!-- c-event-article ends here -->
