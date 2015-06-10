<?php if (!defined('ABSPATH')) die();

header('X-Frame-Options: SAMEORIGIN');

global $post;

?>

<!DOCTYPE html>

<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="ie6 template-<?=$template_class?>"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie7 template-<?=$template_class?>"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie8 template-<?=$template_class?>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="template-<?=$template_class?>" <?php language_attributes(); ?>><!--<![endif]-->
<head data-application-url="<?=site_url()?>">
  <?php $this->view('modules/head') ?>
  <?=$this->wp_head?>
</head>
<body>
  <?php $this->view('modules/header', $this->header_model->get_data()); ?>
  <div id="content" class="container main-content" role="main">
    <div class="content-wrapper">
      <?php $this->view('modules/beta_banner'); ?>
      <?php $this->view($page, $page_data) ?>
    </div>
  </div>

  <?php $this->view('modules/feedback'); ?>
  <?php $this->view('modules/footer'); ?>
  <?php $this->view('modules/body_bottom'); ?>
</body>
</html>
