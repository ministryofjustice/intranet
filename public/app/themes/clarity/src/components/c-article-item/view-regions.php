<?php
use MOJ\Intranet\Authors;

if ($config === 'archive') {
    $id = get_the_ID();
} else {
    $id = $data['id'];
}

$post_object    = get_post($id);
$thumbnail_type = 'intranet-large';
$thumbnail_id   = get_post_thumbnail_id($id);
$thumbnail      = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text       = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$oAuthor        = new Authors();
$authors        = $oAuthor->getAuthorInfo($id);
$thumbnail_url  = $thumbnail[0];

?>
<article class="c-article-item js-article-item">
    <?php
    if (isset($thumbnail_url)) {
        ?>
        <a tabindex="-1" aria-hidden="true" href="<?= get_the_permalink($id) ?>" class="thumb_image">
          <img src="<?= $thumbnail_url ?>" alt>
        </a>
        <?php
    } else {
        ?>
        <a href="<?= get_the_permalink($id) ?>" class="thumb_image">
          <img src="<?= esc_url($authors[0]['thumbnail_url'] ?? '') ?>" alt="<?= esc_attr($alt_text ?? '') ?>">
        </a>
        <?php
    }
    ?>
  <div class="text-align">
    <h1>
      <a href="<?= get_the_permalink($id) ?>"><?= get_the_title($id) ?></a>
    </h1>
    <?php
    if ($config === 'show_date') {
        ?>
        <div class="meta">
          <span class="c-article-item__dateline"><?= get_the_time('j M Y', $id) ?></span>
        </div>
        <?php
    }
    ?>

    <?php
    if ($config === 'show_date_and_excerpt') {
        ?>
        <div class="c-article-excerpt">
          <p><?= get_the_excerpt($id) ?></p>
        </div>
        <div class="meta">
          <span class="c-article-item__dateline"><?= get_the_time('j M Y', $id) ?></span>
        </div>
        <?php
    }
    ?>

    <?php
    // If the 'show_excerpt' value has been passed to $config: Display the excerpt.
    if ($config === 'show_excerpt') {
        ?>
        <div class="c-article-exceprt">
          <p><?= get_the_excerpt($id) ?></p>
        </div>
        <div class="meta">
          <span class="c-article-item__dateline"><?= get_the_time('j M Y', $id) ?> by <?= $authors[0]['name'] ?></span>
        </div>
        <?php
    }
    ?>
    <?php
    // If the 'blog' value has been passed to $config: Display the byline.
    if ($config === 'blog') {
        ?>
      <span class="c-article-item__dateline"><?= get_the_time('j M Y', $id) ?></span>
        <?php
    }
    ?>
  </div>

</article>
