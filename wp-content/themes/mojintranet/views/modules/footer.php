<?php if (!defined('ABSPATH')) die(); ?>

<div class="footer">
  <?php $this->view('modules/feedback'); ?>
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <div class="footer-menu">
        <?php if(is_active_sidebar('first-footer-widget-area')): ?>
          <?php dynamic_sidebar('first-footer-widget-area'); ?>
        <?php endif ?>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="copyright-container">
        <img class="crown-copyright-logo" src="<?php echo get_stylesheet_directory_uri();?>/assets/images/crown_copyright_logo.png" alt="crown copyright logo" />
        <br />
        <span>&copy; Crown copyright</span>
      </div>
    </div>
  </div>
</div>
