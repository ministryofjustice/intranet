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
    <div class="col-lg-2 col-md-3 col-sm-12 popup-gallery">
      <a href="<?php echo $gallery['sizes']['large']; ?>" title="<?php echo $gallery['caption']; ?>">
      <img src="<?php echo $gallery['sizes']['thumbnail']; ?>" alt="" title="<?php echo $gallery['caption']; ?>"/>
      </a>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>
<script>
// This will eventually need to be refactored into the appropreate js area. Best to do when incorporating the video popup.
$(document).ready(function() {

  $('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
			}
		}
	});

});
</script>
