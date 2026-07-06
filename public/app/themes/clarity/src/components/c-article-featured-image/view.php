<?php

$post_object    = get_post($id);
$thumbnail_type = 'intranet-large';
$thumbnail_id   = get_post_thumbnail_id($id);
$thumbnail      = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text       = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

$thumbnail_url = "";
if (is_array($thumbnail)) {
    $thumbnail_url = $thumbnail[0];
}

?>
<!-- c-article-featured-image starts here -->
<div class="c-article-featured-image c-article-featured-image__news">
  <img src="<?= esc_url($thumbnail_url) ?>" alt="<?= esc_attr($alt_text ?? '') ?>">
</div>
<!-- c-article-featured-image ends here -->
