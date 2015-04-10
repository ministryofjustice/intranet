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

<div class="grid page-feedback-container">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <a href="mailto:newintranet@digital.justice.gov.uk" class="page-feedback-link">Is there anything wrong with this page?</a>
  </div>
</div>

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

<?php
	wp_footer();
?>
<!--[if !(IE)]><!-->
  <script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/hammer.min.js"></script>
  <script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/jquery.hammer.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/app.js?<?=add_checksum_param('js/app.js')?>"></script>
</body>
</html>
