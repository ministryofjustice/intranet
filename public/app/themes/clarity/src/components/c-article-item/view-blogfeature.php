<?php
/**
 *  Individual homepage featured item
 */

 $id            = get_the_ID();
?>

<article class="c-article-item">

    <h1 class="o-title o-title--subtitle">Latest from our blog</h1>
  <a aria-hidden="true" href="<?= esc_url(get_permalink($id)) ?>">
    <?php the_post_thumbnail('square-feature', 'alt'); ?>
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
