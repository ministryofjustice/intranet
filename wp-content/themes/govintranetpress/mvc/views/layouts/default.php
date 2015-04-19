<?php if (!defined('ABSPATH')) die();

header('X-Frame-Options: SAMEORIGIN');

?>

<!DOCTYPE html>

<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<head data-application-url="<?=site_url()?>">
  <?php $this->view('modules/head') ?>
  <?=$this->wp_head?>
</head>
<body>
  <?php $this->view('modules/header'); ?>

  <div id="content" class="container main-content" role="main">
    <div class="content-wrapper">
      <?php $this->view('modules/beta_banner'); ?>
      <?php if($breadcrumbs): ?>
        <?php $this->view('modules/breadcrumbs'); ?>
      <?php endif ?>
      <?php $this->view($page, $page_data) ?>
    </div>
  </div>

  <?php $this->view('modules/feedback'); ?>
  <?php $this->view('modules/footer'); ?>
  <?php $this->view('modules/body_bottom'); ?>
</body>
</html>
