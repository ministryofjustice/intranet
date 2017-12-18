<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <meta name="theme-color" content="<?php echo $agency_colour; ?>">
  <title><?php echo $agency_title; ?> | Ministry of Justice intranet</title>
  <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/vendors/jquery.min.js" charset="utf-8"></script>

  <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/core.min.css">
  <link rel="stylesheet" type="text/css"  media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/print.min.css">
  <link rel="icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-180x180.png">
  <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/apple-touch-icon-180x180.png" />
  <!--[if lt IE 8]>
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/vendors/respond.min.js"></script>
  <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/vendors/ie8-js-html5shiv.js"></script>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/ie.min.css" media="screen">
  <![endif]-->
  <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/vendors/respond.min.js"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/vendors/ie8-js-html5shiv.js"></script>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/ie8.min.css" media="screen">
  <![endif]-->
  <?php
  /**
   * wp_head() required WP function do not remove. Used by plugins to hook into and for theme development.
   */
   ?>
   <?php wp_head(); ?>
</head>
<?php
if (!defined('GT_CODE')) {
  if ($_SERVER['HTTP_HOST'] == 'intranet.dev.wp.dsd.io') {
    define('GT_CODE', 'GTM-W5VKJK');
  }
  else {
    define('GT_CODE', 'GTM-P545JM');
  }
}
?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?=GT_CODE?>"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?=GT_CODE?>');</script>
<!-- End Google Tag Manager -->
<body <?php
/**
 * Adds agency specific classes to the page.
 */
  $agency_class = 'agency-'.$agency_shortcode;
  body_class($class = $agency_class); ?>>
  <?php
  /**
   * Devtools. Highlights components, objects, classes and utilities on the page when activated.
   * Activate by adding ?devtools=true to the end of the page url.
   */
   ?>
  <?php
    if ($_SERVER['HTTP_HOST'] == 'intranet.dev.wp.dsd.io') {
      if(isset($_GET['devtools'])){
        if($_GET['devtools'] === 'true') {
          get_component('c-clarity-toolbar');
        }
      }
    }
  ?>
  <a class="u-skip-link" href="#maincontent">Skip to main content</a>
  <?php get_component('c-header-container'); ?>
