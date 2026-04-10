<?php

defined('ABSPATH') || die();

$post_object    = get_post($id);
$thumbnail_type = 'intranet-large';
$thumbnail_id   = get_post_thumbnail_id($id);
$thumbnail      = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text       = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

$thumbnail_url = "";
if (is_array($thumbnail)) {
  $thumbnail_url = $thumbnail[0];
}

$featured_img = "<img src='" . esc_url($thumbnail_url) . "' alt='" . esc_attr($alt_text ?? '')  . "'>";

// Get the content so that we can parse it into 3 distinct parts - for the layout.
$content = apply_filters('the_content', get_post_field('post_content', $id));

$pattern = '/^(.*?(?:<h[1-6][^>]*>.*?<\/h[1-6]>|\z))((?:\s*<p>.*?<\/p>){0,2})(.*)$/sx';

preg_match($pattern, $content, $matches);

$before_and_heading = trim($matches[1] ?? '');
$paragraphs_after   = trim($matches[2] ?? '');
$rest_of_content    = trim($matches[3] ?? '');

?>
<!-- c-inline-featured-image starts here -->
<div class="c-inline-featured-image">

  <?php do_action('before_rich_text_block'); ?>

  <!-- c-rich-text-block starts here -->
  <section class="c-rich-text-block">

    <?= wp_kses_post($before_and_heading); ?>

    <div class="c-inline-featured-image__row">
      <div class="c-inline-featured-image__column--text">
        <?= wp_kses_post($paragraphs_after);  ?>
      </div>
      <div class="c-inline-featured-image__column--image">
        <img src="<?= esc_url($thumbnail_url) ?>" alt="<?=  esc_attr($alt_text ?? '') ?>">
      </div>
    </div>

    <?= wp_kses_post($rest_of_content); ?>

  </section>
  <!-- c-rich-text-block ends here -->

</div>
<!-- c-inline-featured-image ends here -->
