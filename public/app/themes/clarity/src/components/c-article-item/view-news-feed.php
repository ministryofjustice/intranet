<?php
/**
 *  Individual news feed list item
 *  http://intranet.docker/newspage/
 *
 *  @package Clarity
 */
 $id            = $post->ID;
 $thumbnail     = get_the_post_thumbnail_url($id, 'user-thumb');
 $thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
 $link = get_the_permalink($post->ID);

// This component sometimes requires `$set_cpt` depending where this component gets called.
if (! isset($set_cpt)) {
    $set_cpt = '';
}
?>

<article class="c-article-item js-article-item" data-type="<?= $set_cpt ?>">

    <?php if ($thumbnail) : ?>
        <a tabindex="-1" aria-hidden="true" href="<?= esc_url(get_permalink($id)) ?>" class="thumbnail">
            <img src="<?= esc_url($thumbnail) ?>" alt="<?= $thumbnail_alt ?>">
        </a>
    <?php endif; ?>

    <div class="content">

        <h1>
            <a href="<?= esc_url($link) ?>"><?= get_the_title($id); ?></a>
        </h1>

        <div class="meta">
            <span class="c-article-item__dateline">
                <?= get_gmt_from_date($post->post_date, 'j M Y') ?>
            </span>
        </div>

        <?php if (!is_singular('regional_news') && !is_singular('news')) : ?>
            <div class="c-article-excerpt">
                <p><?= get_the_excerpt($id); ?></p>
            </div>
        <?php endif; ?>

    </div>

</article>
