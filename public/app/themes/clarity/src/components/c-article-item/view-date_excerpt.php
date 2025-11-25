<?php
/**
 *  Individual homepage featured item
 */

 $id            = get_the_ID();
?>

<article class="c-article-item js-article-item">

  <a aria-hidden="true" href="<?= esc_url(get_permalink($id)) ?>">
    <?php the_post_thumbnail('intranet-large', 'alt') ?>
  </a>

  <div class="text-align">
    <h1>
      <a href="<?= esc_url(get_permalink($id)) ?>"><?= get_the_title($id) ?></a>
    </h1>

    <div class="c-article-excerpt">
      <p><?= get_the_excerpt($id) ?></p>
    </div>
  </div>

</article>
