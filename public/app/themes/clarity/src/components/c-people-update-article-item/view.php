<?php

/**
 *  Individual people-update (People Promise update)
 */

defined('ABSPATH') || die();

$id     = get_the_ID();
$pillars = get_the_terms($id, 'opg_pillar');
$pillar = is_array($pillars) && isset($pillars[0]) ? $pillars[0] : null;
?>

<article class="c-people-update-article-item">

  <div href="<?= esc_url(get_permalink($id)) ?>" class="c-people-update-article-item--thumbnail">
    <?php the_post_thumbnail('feature-thumbnail'); ?>
    <?php if ($pillar) : ?>
      <span class="c-people-update-article-item__pillar c-people-update-article-item__pillar--<?= esc_attr($pillar->slug) ?>">
        <?= esc_html($pillar->name) ?>
      </span>
    <?php endif; ?>
  </div>

  <div href="<?= esc_url(get_permalink($id)) ?>" class="c-people-update-article-item--text">
    <h1 class="o-title"><?php echo get_the_title() ?></h1>
    <?= apply_filters('the_content', get_post_field('post_content', $id)); ?>
  </div>

</article>
