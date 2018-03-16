<?php if (!defined('ABSPATH')) die(); ?>

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

        <?php $this->view('modules/share_bar', $share_bar) ?>
      </div>
  </div>
  <?php $this->view('modules/side_navigation') ?>
  <?php $this->view('pages/media_grid/lightbox') ?>
</div>

<script>
// From jQuery object
$(document).ready(function() {

  $('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: false, // turn on gallery feature
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return item.el.attr('title') + ''; // hardcode text into lightbox gallery. I've left it empty for now.
			}
		}
	});

});
</script>
