<?php if (!defined('ABSPATH')) {
    die();
} ?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
  <h3><?php echo get_sub_field('video_gallery_title'); ?></h3>
  </div>

  <?php
  $youtube_id = get_sub_field('video_gallery');
  if ($youtube_id):
    foreach ($youtube_id as $id): ?>

    <div class="col-lg-4 col-md-5 col-sm-12 video-thumbnail">

      <!--[if !IE]><!-->
      <a href='"<?php echo '<iframe name="wmode" value="transparent" src="https://www.youtube.com/embed/'. $id['youtube_id'] . '" width="854" height="480" frameborder="0"></iframe>'; ?>"'>
      <img src="<?php echo 'https://img.youtube.com/vi/' . $id['youtube_id'] . '/maxresdefault.jpg'; ?>" class="thumbnail" width="300" alt="" />
      </a>
      <!--<![endif]-->

      <!--[if lte IE 9]>
      <a href='"<?php echo '<iframe name="wmode" value="transparent" src="https://www.youtube.com/v/'. $id['youtube_id'] . '" width="854" height="480" frameborder="0"></iframe>'; ?>"'>
      <img src="<?php echo 'https://img.youtube.com/vi/' . $id['youtube_id'] . '/maxresdefault.jpg'; ?>" class="thumbnail" width="300" alt="" />
      </a>
      <![endif]-->

    </div>

  <?php endforeach; ?>
<?php endif; ?>
</div>
