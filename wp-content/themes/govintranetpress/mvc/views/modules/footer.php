<div class="footerwrapper">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="footer">
        <?php if(is_active_sidebar('first-footer-widget-area') && $_SESSION['full_site']): ?>
          <?php dynamic_sidebar('first-footer-widget-area'); ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
