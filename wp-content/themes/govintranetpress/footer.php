<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>
  </div>
</div>

<div id='footerwrapper'>
  <div class="container">
    <div id='footer' class="grid">
      <div id='footer-left' class="col-lg-4 col-md-6 col-sm-12">
        <?php if ( is_active_sidebar( 'first-footer-widget-area' ) ) : ?>
          <?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
        <?php endif; ?>
      </div>
      <div id='footer-3' class="col-lg-4 col-md-6 col-sm-12">
        <?php if ( is_active_sidebar( 'right1-footer-widget-area' ) ) : ?>
          <?php dynamic_sidebar( 'right1-footer-widget-area' ); ?>
        <?php endif; ?>
      </div>
      <div id='footer-right' class="col-lg-4 col-md-6 col-sm-12">
        <?php if ( is_active_sidebar( 'right2-footer-widget-area' ) ) : ?>
          <?php dynamic_sidebar( 'right2-footer-widget-area' ); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div><!-- container -->

<?php
	wp_footer();
?>
<!--[if !(IE)]><!-->
  <script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/hammer.min.js"></script>
  <script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/jquery.hammer.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/app.js"></script>
</body>
</html>
