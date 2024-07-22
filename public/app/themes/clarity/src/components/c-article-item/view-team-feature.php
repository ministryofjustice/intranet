<?php
/**
 *  Team homepage feature list item
 */
use MOJ\Intranet\Authors;

$oAuthor       = new Authors();
$id            = get_the_ID();
$authors       = $oAuthor->getAuthorInfo($id);
$thumbnail     = get_the_post_thumbnail_url($id, 'feature-thumbnail');
$thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);

?>

<!-- c-article-item-view-team-feature starts -->
<article class="c-article-item js-article-item">

    <?php if ($thumbnail) : ?>
        <a tabindex="-1" aria-hidden="true" href="<?= esc_url(get_permalink($id)) ?>">
            <img src="<?= esc_url($thumbnail) ?> " alt>
      </a>

    <?php else : ?>
        <!-- no feature image provided -->

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

</article>
<!-- c-article-item/view-team-feature ends -->
