<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <!-- the line below should be changed to match the appropriate agency colour -->
  <meta name="theme-color" content="<?php echo $agency_colour; ?>">
  <title><?php echo $agency_title; ?> | MoJ Intranet</title>
  <link rel="stylesheet" href="<?php echo get_assets_folder(); ?>/css/core.min.css" media="screen">
  <link rel="stylesheet" href="<?php echo get_assets_folder(); ?>/css/print.min.css" media="print">
  <script src="<?php echo get_assets_folder(); ?>/vendors/jquery.min.js" charset="utf-8"></script>
  <link rel="icon" sizes="180x180" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-180x180.png">
  <link rel="shortcut icon" href="<?php echo get_assets_folder(); ?>/icons/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_assets_folder(); ?>/icons/apple-touch-icon-180x180.png" />
  <link rel="canonical" href="//mojintranet.test/" />
  <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo get_assets_folder(); ?>/vendors/respond.min.js"></script>
    <script type="text/javascript" src="<?php echo get_assets_folder(); ?>/vendors/ie8-js-html5shiv.js"></script>
    <link rel="stylesheet" href="<?php echo get_assets_folder(); ?>/css/ie.min.css" media="screen">
  <![endif]-->
  <?php
  /**
   * wp_head() required WP function do not remove. Used by plugins to hook into and for theme development.
   */
   ?>
   <?php wp_head(); ?>
</head>
<?php
  $agency_class = 'agency-'.$agency_shortcode;
?>
<body <?php body_class($class = $agency_class); ?>>
  <?php if($_GET['devtools'] === 'true') get_component('c-clarity-toolbar'); ?>
  <a class="u-skip-link" href="#maincontent">Skip to main content</a>
  <?php get_component('c-header-container'); ?>
