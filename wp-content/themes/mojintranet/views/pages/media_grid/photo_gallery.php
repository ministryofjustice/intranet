<?php if (!defined('ABSPATH')) {
    die();
} ?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
  <h3><?php echo get_sub_field('photo_gallery_title'); ?></h3>
</div>
  <?php
  $photo_gallery = get_sub_field('photo_gallery');
  if ($photo_gallery):
    foreach ($photo_gallery as $gallery): ?>
    <div class="col-lg-2 col-md-3 col-sm-12 thumbnail">
      <a href="<?php echo $gallery['sizes']['large']; ?>">
        <img src="<?php echo $gallery['sizes']['thumbnail']; ?>" alt="<?php echo $gallery['alt']; ?>" />
      </a>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>
