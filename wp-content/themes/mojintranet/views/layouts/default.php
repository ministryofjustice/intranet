<?php if (!defined('ABSPATH')) die();
include(get_template_directory() . '/inc/headers.php');
?>

<!DOCTYPE html>

<!--[if lte IE 7]>
  <html <?php language_attributes() ?> class="ie7 lte-ie7 lte-ie8 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if IE 8]>
  <html <?php language_attributes() ?> class="ie8 lte-ie8 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if IE 9]>
  <html <?php language_attributes() ?> class="ie9 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
  <html class="template-<?=$template_class?>" <?php language_attributes() ?>>
<!--<![endif]-->

<head data-application-url="<?=site_url()?>" data-template-uri="<?=get_template_directory_uri()?>">
  <?php $this->view('modules/head') ?>
  <?=$this->wp_head?>
</head>
<body>
  <?php $this->view('modules/google_tag_manager') ?>
  <?php $this->view('modules/header', $this->model->header->get_data()) ?>
  <div id="content" class="container main-content" role="main" tabindex="-1">
    <div class="content-wrapper">
      <?php $this->view('modules/beta_banner') ?>
      <!--[if IE 6]>
        <?php $this->view('modules/ie6_message') ?>
      <![endif]-->
      <?php if (!$no_breadcrumbs): ?>
        <?php $this->view('modules/breadcrumbs', array('breadcrumbs' => $this->model->breadcrumbs->get_data())) ?>
      <?php endif ?>
      <?php $this->view($page, $page_data) ?>
    </div>
  </div>

  <?php $this->view('modules/footer') ?>
  <?php $this->view('modules/body_bottom') ?>
</body>
</html>
