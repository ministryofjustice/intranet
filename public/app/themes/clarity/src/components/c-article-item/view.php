<?php

/**
 *
 * Single list item
 **/
global $post;

$id            = $post->ID;
$thumbnail_id = get_post_thumbnail_id($id);
$thumbnail     = wp_get_attachment_image_src($thumbnail_id, 'list-thumbnail');
$thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$thumbnail_url = $thumbnail[0];

?>

<article class="c-article-item js-article-item">

    <?php if (! empty($thumbnail_url)) : ?>
    <a tabindex="-1" aria-hidden="true" href="<?= esc_url(get_permalink($id)) ?>">
      <img src="<?= esc_url($thumbnail_url) ?>" alt="<?= esc_attr($thumbnail_alt ?? '') ?>">
    </a>
    <?php endif; ?>

  <div class="text-align">
    <h1><a href="<?= esc_url(get_permalink($id)) ?>"><?= get_the_title($id) ?></a></h1>

    <div class="meta">
      <span class="c-article-item__dateline">
        <?= get_the_time('j M Y', $id) ?>
    </span>
    </div>
  </div>

</article>
