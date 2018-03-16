<?php if (!defined('ABSPATH')) die();
include(get_template_directory() . '/inc/headers.php');

if (!isset($no_breadcrumbs)) $no_breadcrumbs = false;
if (!isset($page_data)) $page_data = [];

?>

<!DOCTYPE html>

<!--[if IE 6]>
  <html <?php language_attributes() ?> class="user-not-initialised ie6 lte-ie7 lte-ie8 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if lte IE 7]>
  <html <?php language_attributes() ?> class="user-not-initialised ie7 lte-ie7 lte-ie8 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if IE 8]>
  <html <?php language_attributes() ?> class="user-not-initialised ie8 lte-ie8 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if IE 9]>
  <html <?php language_attributes() ?> class="user-not-initialised ie9 lte-ie9 template-<?=$template_class?>">
<![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
  <html class="user-not-initialised template-<?=$template_class?>" <?php language_attributes() ?>>
<!--<![endif]-->

<head data-application-url="<?php echo get_home_url()?>" data-template-uri="<?=get_template_directory_uri()?>" data-content-agency="<?=$this->model->content->get_agency()?>">
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

  <?php $this->view('modules/skeleton_screens') ?>

  <?php $this->view('modules/footer'); ?>
  <?php $this->view('modules/body_bottom'); ?>
  <?=$this->wp_footer?>
</body>
</html>
