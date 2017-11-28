<?php
/**
 * The template for displaying the footer
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

?>

<footer class="c-footer">
  <div class="u-wrapper">
    <div class="l-primary">
      <?php get_component('c-feedback-container'); ?>
      <?php get_component('c-footer-nav'); ?>
    </div>
    <div class="l-secondary">
      <?php get_component('c-copyright-notice'); ?>
  </div>
</footer>
<?php $time = current_time( 'Y-m-d-H-i-s' ); ?>
<script src="<?php echo get_assets_folder(); ?>/js/core.min.js?<?php echo $time ?>" charset="utf-8"></script>

<?php
  /**
   * wp_footer() required WP function do not remove. Used by plugins to hook into and for theme development.
   */
  wp_footer();
?>

</body>
</html>

