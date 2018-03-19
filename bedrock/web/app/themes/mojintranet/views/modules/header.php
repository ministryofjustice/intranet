<?php if (!defined('ABSPATH')) die(); ?>

<div class="header" role="banner" data-agencies="<?=$stringified_agencies?>">
  <div class="grid skip-to-content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <a href="#content">Skip to main content</a>
    </div>
  </div>

  <div class="grid header-top">
    <div class="site-logo-hq col-lg-12 col-md-12 col-sm-12">
      <img src="<?=get_template_directory_uri()?>/assets/images/logos/moj_logo.png" alt="Ministry of Justice logo" />
    </div>
    <div class="site-logo col-lg-8 col-md-8 col-sm-12">
      <a href="<?php echo get_home_url()?>" title="Intranet" rel="home">
        <img src="<?=get_template_directory_uri()?>/assets/images/logos/moj_logo.png" alt="Ministry of Justice logo" />
      </a>
    </div>
    <div class="user-bar col-lg-4 col-md-4 col-sm-12">
      <ul class="user-menu">
        <li class="select-agency-trigger-container">
          <a href="#" class="select-agency-trigger">Switch to other intranet</a>

          <div class="tooltip my-agency-tooltip">
            <span class="triangle"></span>
            <p>Please select your agency or public body</p>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <div class="agency-overlay">
    <div class="grid">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <?php $this->view('modules/select_agency', array('stringified_agencies' => $stringified_agencies)) ?>
      </div>
    </div>
  </div>

  <div class="header-search">
    <div class="grid">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <?php $this->view('modules/search_form') ?>
      </div>
    </div>
  </div>

  <div class="header-menu">
    <div class="grid">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <?php $this->view('modules/main_menu', ['main_menu' => $main_menu]) ?>
      </div>
    </div>
  </div>
</div>
