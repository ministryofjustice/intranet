<?php

/**
 *  Individual blog feed list item
 *  http://intranet.docker/blogs
 *
 *  @package Clarity
 */

use MOJ\Intranet\Authors;


$id            = $post->ID;

$thumbnail     = get_the_post_thumbnail_url($id, 'user-thumb');
$thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);

// TODO: Why is this here? It's not used.
// If it should be here, make sure to update `Search->mapPostResult()`
$oAuthor = new Authors();
$authors = $oAuthor->getAuthorInfo($id);

$author = $post->post_author;
$author_display_name = $author ? get_the_author_meta('display_name', $author) : '';

if (!$thumbnail) {
    // Mutate thumbnail with author image.
    $thumbnail = $author ? get_the_author_meta('thumbnail_avatar', $author) : false;
    $thumbnail_alt = $author_display_name;
}

?>

<article class="c-article-item c-article-item--blog js-article-item">

    <?php // Conditional if feature image set or co-author image set ?>
    <?php if ($thumbnail) : ?>

        <a aria-hidden="true" href="<?php the_permalink($id); ?>">
            <img src="<?= $thumbnail ?>" alt="<?= $thumbnail_alt; ?>" class="thumbnail">
        </a>

    <?php endif; ?>

    <div class="content">

        <h1>
            <a href="<?php the_permalink($id); ?>"><?= get_the_title($id) ?></a>
        </h1>

        <div class="meta">
            <span class="c-article-item__dateline">
                By 
                <strong><?= $author_display_name ?></strong> |
                <span class="c-article-item__dateline__date">
                    <?= get_gmt_from_date($post->post_date, 'j M Y') ?>
                </span>
            </span>
        </div>

        <?php // On single blog pages where this is listed we don't have room for the excerpt ?>
        <?php if (! is_singular('post')) : ?>

            <div class="c-article-excerpt">
                <p><?= get_the_excerpt($id) ?></p>
            </div>

        <?php endif; ?>
    </div>

</article>