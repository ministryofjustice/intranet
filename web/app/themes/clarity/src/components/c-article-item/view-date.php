<?php

/**
 *  Individual homepage news item
 *
 * @package Clarity
 */

$id            = get_the_ID();
$thumbnail     = get_the_post_thumbnail_url($id, 'user-thumb');
$thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
?>

<article class="c-article-item js-article-item">

    <?php if ($thumbnail) : ?>
  <a href="<?php echo get_the_permalink($id); ?>" class="thumb_image">
        <?php the_post_thumbnail('feature-thumbnail', 'alt=' . $thumbnail_alt); ?>
  </a>

  <div class="text-align">

    <?php else : ?>
  <!-- No news image provided -->

  <div class="">

    <?php endif; ?>

    <h1>
      <a href="<?php echo get_the_permalink($id); ?>"><?php echo get_the_title($id); ?></a>
    </h1>

    <div class="meta">
      <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id); ?></span>
    </div>

  </div>

</article>
