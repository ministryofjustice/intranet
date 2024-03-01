<?php
/**
 *  Individual homepage blog item
 */
use MOJ\Intranet\Authors;

$oAuthor       = new Authors();
$id            = get_the_ID();
$authors       = $oAuthor->getAuthorInfo($id);
$thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
?>

<article class="c-blog-article-item">

  <a class="c-blog-article-item--thumbnail" href="<?php echo esc_url(get_permalink($id)); ?>">
    <?php the_post_thumbnail('feature-thumbnail', 'alt'); ?>
    <h2 class="c-blog-article-item--title">
      <?php echo get_the_title($id); ?>
    </h2>
  </a>

  <div class="text-align">
    <section class="c-article-byline">
      <div class="c-article-byline__intro">
        By <strong><?php echo esc_attr($authors[0]['name']); ?></strong>
      </div>
      <div class="c-article-byline__date">
        <?php
        // Show date on posts that share the same date
        echo get_the_date('d F Y', $id);
        ?>
      </div>
    </section>

    <div class="c-blog-article-item__excerpt"><p><?php echo get_the_excerpt($id); ?></p></div>
  </div>

</article>
