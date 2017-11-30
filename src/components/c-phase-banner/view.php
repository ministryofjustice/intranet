<?php if( get_field('beta_banner_text', 'option') ): ?>
  <section class="c-phase-banner">
    <a class="c-phase-banner__icon c-phase-banner__icon--beta" href="/what-beta-means/">Beta</a>
    <p class="c-phase-banner__message">
      <?php the_field('beta_banner_text', 'option'); ?>
    </p>
  </section>
<?php endif; ?>