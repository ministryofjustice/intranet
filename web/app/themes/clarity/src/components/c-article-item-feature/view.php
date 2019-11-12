<?php
/**
 *  Individual homepage featured item
 */

 $id            = get_the_ID();
 $thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
?>

<!-- c-article-item-feature starts here -->
<article class="c-article-item-feature">
  <a href="<?php echo esc_url(get_permalink($id)); ?>" class="c-article-item-feature--thumbnail">
    <?php the_post_thumbnail('intranet-large', 'alt=' . $thumbnail_alt); ?>
  </a>

  <div class="text-align">
    <span class="c-article-byline__date">
    <?php
        // Show date on posts that share the same date
        echo get_the_date('d F Y', $id);
    ?>
    </span>
    <h1>
      <a href="<?php echo esc_url(get_permalink($id)); ?>" class="c-article-item-feature--title"><?php echo get_the_title($id); ?></a>
    </h1>

    <div class="c-article-item-feature__excerpt">
      <p><?php echo get_the_excerpt($id); ?></p>
    </div>
  </div>

</article>
<!-- c-article-item-feature ends here -->
