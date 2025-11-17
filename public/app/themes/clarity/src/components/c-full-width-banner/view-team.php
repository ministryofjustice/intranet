
  <!-- c-full-width-team-banner starts here -->
  <section class="c-full-width-banner__team">
    <?php

      $teamBanner = get_field('full_width_page_banner');

    if (! empty($teamBanner)) :
        ?>

        <img src="<?= esc_url($teamBanner['url']) ?>" alt="<?= esc_attr($teamBanner['alt'] ?? '') ?>" />

    <?php else : ?>
        <!-- No banner selected or available to display. -->

    <?php endif; ?>

  </section>
  <!-- c-full-width-team-banner ends here -->
