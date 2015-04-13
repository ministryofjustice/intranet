<?php if (!defined('ABSPATH')) die(); ?>

<!--[if !(IE)]><!-->
  <script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/hammer.min.js"></script>
  <script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/jquery.hammer.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/js/app.js?<?=add_checksum_param('js/app.js')?>"></script>

<?php wp_footer(); ?>
