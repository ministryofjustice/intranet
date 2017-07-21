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
    <div class="col-lg-2 col-md-3 col-sm-12">
      <a class="image-popup-no-margins" href="<?php echo $gallery['sizes']['large']; ?>" title="<?php echo $gallery['caption']; ?>">
      <img src="<?php echo $gallery['sizes']['thumbnail']; ?>" alt="" title="<?php echo $gallery['caption']; ?>"/>
      </a>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>
<script>
// This will eventually need to be refactored into the appropreate js area. Best to do when incorporating the video popup.
$(document).ready(function() {

	$('.image-popup-no-margins').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		closeBtnInside: true,
		fixedContentPos: true,
		mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
		image: {
			verticalFit: true
		},
		zoom: {
			enabled: true,
			duration: 300 // don't foget to change the duration also in CSS
		}
	});

});
</script>
