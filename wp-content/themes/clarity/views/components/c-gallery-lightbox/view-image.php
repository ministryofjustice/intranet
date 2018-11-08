<?php //lightbox ?>
<section class="c-image-gallery js-feature-video">
  <a class="popup-image" href="<?php echo $gallery_image; ?>">
    <?php echo wp_get_attachment_image( $gallery_image_id, $size ); ?>
  </a>
</section>
