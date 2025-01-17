<?php
/**
 *  Individual blog list item
 */
use MOJ\Intranet\Authors;

$oAuthor = new Authors();

$id      = get_the_ID();
$authors = $oAuthor->getAuthorInfo($id);
?>

<article class="c-article-item js-article-item">

    <?php if (! empty($authors[0]['thumbnail_url'])) : ?>
    <a aria-hidden="true" href="<?= esc_url(get_permalink($id)) ?>">
      <img src="
        <?php
        // Display guest author image
        echo $authors[0]['thumbnail_url'];
        ?>
    " alt>
    </a>

  <div class="text-align">

    <?php else : ?>
    <div class="">
        <?php // no guest author image ?>
    <!-- No guest author image provided -->

    <?php endif; ?>

    <h1>
      <a href="<?= esc_url(get_permalink($id)) ?>">
        <?= get_the_title($id) ?>
    </a>
    </h1>

    <div class="meta">
      <span class="c-article-item__dateline">
        <?= get_the_time('j M Y', $id) ?>
    </span>
    </div>

  </div>

</article>
