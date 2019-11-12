<?php // lightbox ?>
<section class="c-image-gallery js-feature-video">
  <a class="popup-image" href="<?php echo $gallery_image; ?>">
    <img src="<?php echo wp_get_attachment_image_src($gallery_image_id, $size)[0]; ?>" alt="">
  </a>
</section>
