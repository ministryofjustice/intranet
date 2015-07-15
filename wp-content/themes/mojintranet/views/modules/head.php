<?php if (!defined('ABSPATH')) die(); ?>

<title><?php wp_title('', true, 'right'); ?></title>

<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="stylesheet" type="text/css" media="all" href="<?=get_template_directory_uri()?>/assets/css/style.css?<?=add_checksum_param('css/style.css')?>" />
<link rel="stylesheet" type="text/css" media="print" href="<?=get_template_directory_uri()?>/assets/css/print.css" />

<!--[if !IE]><!-->
  <link rel="stylesheet" type="text/css" media="all" href="<?=get_template_directory_uri()?>/assets/css/fonts.css" />
<!--<![endif]-->

<!--[if lte IE 9]>
  <link rel="stylesheet" type="text/css" media="screen" href="<?=get_template_directory_uri()?>/assets/css/ie.css?<?=add_checksum_param('css/ie.css')?>" />
<![endif]-->

<!--[if lt IE 9]>
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<![endif]-->
<!--[if gt IE 8]>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<![endif]-->
