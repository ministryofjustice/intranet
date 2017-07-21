<?php if (!defined('ABSPATH')) {
    die();
} ?>

<div class="template-container"
     data-page-id="<?=$id?>">

  <div class="grid content-container">
     <?php if (have_rows('media_grid')): ?>
       <?php while (have_rows('media_grid')): the_row(); ?>

      <?php $banner = get_sub_field('top_banner');
        if ($banner): ?>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <img class="campaign-banner" width="990" src="<?php echo $banner; ?>"/>
          </div>
       <?php endif; ?>

      <?php endwhile; ?>
    <?php endif; ?>

    <?php if ($lhs_menu_on): ?>
      <div class="col-lg-3 col-md-4 col-sm-12">
        <nav class="menu-list-container">
          <ul class="menu-list"></ul>
        </nav>
      </div>
    <?php endif ?>

    <div class="<?=$content_classes?>">
      <h1 class="page-title"><?=$title?></h1>
      <div class="editable">
        <?=$content?>
      </div>

    </div>
  </div>


      <div class="grid content-container">
        <div class="col-lg-12 col-md-12 col-sm-12">

          <?php if (have_rows('media_grid')): ?>
            <?php while (have_rows('media_grid')): the_row(); ?>

              <h2><?php echo get_sub_field('media_section_title'); ?></h2>

            <div class="editable">
              <?php echo get_sub_field('body_text'); ?>
            </div><br>

              <?php $this->view('pages/media_grid/feature_media') ?>
              <?php $this->view('pages/media_grid/photo_gallery') ?>
              <?php $this->view('pages/media_grid/video_gallery') ?>
              <?php $this->view('pages/media_grid/quotes') ?>

            <?php endwhile; ?>
          <?php endif; ?>

        </div>
      </div>

  <?php $this->view('modules/side_navigation') ?>
  <?php $this->view('pages/media_grid/lightbox') ?>
  
</div>
<script>
// From jQuery object
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
