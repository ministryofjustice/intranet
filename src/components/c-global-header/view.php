<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <title>MoJ Intranet</title>
  <link rel="stylesheet" href="<?php echo get_assets_folder(); ?>/css/core.min.css" media="screen">
  <link rel="stylesheet" href="<?php echo get_assets_folder(); ?>/css/print.min.css" media="print">
  <script src="<?php echo get_assets_folder(); ?>/vendors/jquery.min.js" charset="utf-8"></script>
  <link rel="canonical" href="//mojintranet/" />
  <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo get_assets_folder(); ?>/vendors/respond.min.js"></script>
    <script type="text/javascript" src="<?php echo get_assets_folder(); ?>/vendors/ie8-js-html5shiv.js"></script>
    <link rel="stylesheet" href="<?php echo get_assets_folder(); ?>/css/ie.min.css" media="screen">
  <![endif]-->
</head>
<body class="agency-hq">
  <?php if($_GET['devtools'] === 'true') get_component('c-clarity-toolbar'); ?>
  <a class="u-skip-link" href="#maincontent">Skip to main content</a>
  <?php get_component('c-header-container'); ?>
