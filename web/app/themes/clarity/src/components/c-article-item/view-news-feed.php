<?php
/**
 *  Individual news feed list item
 *  http://intranet.docker/newspage/
 *
 *  @package Clarity
 */
 $id            = $post['id'];
 $thumbnail     = get_the_post_thumbnail_url($id, 'user-thumb');
 $thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);

// This component sometimes requires `$set_cpt` depending where this component gets called.
if (! isset($set_cpt)) {
    $set_cpt = '';
}
?>

<article class="c-article-item js-article-item" data-type="<?php echo $set_cpt; ?>">

        <?php

        if ($thumbnail) :
            echo '<a href="' . esc_url(get_permalink($id)) . '" class="thumbnail">';
            echo '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($thumbnail_alt) . '">';
            echo '</a>';
        else :
                echo '<!-- No news author or image supplied-->';
        endif;

        ?>

    <div class="content">

        <h1>
            <a href="<?php echo esc_url($post['link']); ?>"><?php echo esc_attr($post['title']['rendered']); ?></a>
        </h1>

        <div class="meta">
            <span class="c-article-item__dateline"><?php echo get_gmt_from_date($post['date'], 'j M Y'); ?></span>
        </div>

    <?php if (is_singular('regional_news') || is_singular('news')) : ?>
    <div class="c-article-excerpt"><!-- No excerpt available --></div>

    <?php else : ?>
    <div class="c-article-excerpt">
        <p><?php echo $post['excerpt']['rendered']; ?></p>
    </div>

    <?php endif; ?>

    </div>

</article>
