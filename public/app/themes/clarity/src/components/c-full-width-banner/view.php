<?php
use MOJ\Intranet\Agency;

$agency = get_intranet_code();

$check_box_to_turn_banner_on = get_field($agency . '_check_box_to_turn_banner_on', 'option');
$homepage_banner_image = get_field($agency . '_homepage_banner_image', 'option');
$homepage_banner_link = get_field($agency . '_homepage_banner_link', 'option');
$homepage_banner_alt_text = get_field($agency . '_homepage_banner_alt_text', 'option');

?>
<?php if ($check_box_to_turn_banner_on) : ?>
  <!-- c-full-width-banner starts here -->
  <section class="c-full-width-banner">
    <a href="<?= esc_url($homepage_banner_link) ?>" class="full-width-banner">
        <img src="<?= esc_url($homepage_banner_image ?? '') ?>" alt="<?= esc_attr($homepage_banner_alt_text ?? '') ?>">
    </a>
  </section>
  <!-- c-full-width-banner ends here -->
<?php else : ?>
  <!-- No banner image selected, so nothing loaded. -->
<?php endif; ?>
