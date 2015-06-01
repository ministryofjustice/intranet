<?php if (!defined('ABSPATH')) die(); ?>

<!--[if lte IE 7]>
  <script type="text/javascript" src="<?=get_template_directory_uri()?>/js/json3.min.js"></script>
<![endif]-->
<!--[if lt IE 9]>
  <script type="text/javascript" src="<?=get_template_directory_uri()?>/js/html5-shiv.min.js"></script>
  <script type="text/javascript" src="<?=get_template_directory_uri()?>/js/respond.min.js"></script>
<![endif]-->

<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/app.js?<?=add_checksum_param('js/app.js')?>"></script>

<?php wp_footer(); ?>
