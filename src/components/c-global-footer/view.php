<footer class="c-footer">
  <div class="u-wrapper">
    <div class="l-primary">
      <?php get_component('c-feedback-container'); ?>
      <?php get_component('c-footer-nav'); ?>
    </div>
    <div class="l-secondary c-copyright">
      <?php get_component('c-copyright-notice'); ?>
  </div>
</footer>
<script src="<?php echo get_assets_folder(); ?>/js/core.min.js" charset="utf-8"></script>

<?php
  /**
   * wp_footer() required WP function do not remove. Used by plugins to hook into and for theme development.
   */
  wp_footer();
?>

</body>
</html>
