<?php
/**
 *  Individual homepage latest news
 */

 $id            = get_the_ID();
 $thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
?>

<article class="c-news-article-item">

  <a href="<?php echo esc_url(get_permalink($id)); ?>" class="c-news-article-item--thumbnail">
    <?php the_post_thumbnail('feature-thumbnail', 'alt'); ?>
    <h2 class="c-news-article-item--title">
      <?php echo get_the_title($id); ?>
    </h2>
  </a>

  <div class="text-align">
    <div class="c-article-byline__date">
        <?php
        // Show date on posts that share the same date
        echo get_the_date('d F Y', $id);
        ?>
    </div>

    <div class="c-news-article-item__excerpt">
      <p><?php echo get_the_excerpt($id); ?></p>
    </div>
  </div>

</article>
