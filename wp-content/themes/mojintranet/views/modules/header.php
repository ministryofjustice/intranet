<?php if (!defined('ABSPATH')) die(); ?>

<div class="header" role="banner" data-agencies="<?=$stringified_agencies?>">
  <div class="grid skip-to-content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <a href="#content">Skip to main content</a>
    </div>
  </div>

  <div class="mobile-msg mobile-only">
    <p>We are working towards improving your mobile intranet experience - please bear with us.</p>
  </div>
  <div class="grid header-top">
    <div class="site-logo-hq col-lg-12 col-md-12 col-sm-12">
      <img src="<?=get_template_directory_uri()?>/assets/images/logos/moj_logo.png" alt="Ministry of Justice logo" />
    </div>
    <div class="site-logo col-lg-6 col-md-6 col-sm-12">
      <a href="<?=WP_SITEURL?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
        <img src="<?=get_template_directory_uri()?>/assets/images/logos/moj_logo.png" alt="Ministry of Justice logo" />
      </a>
    </div>
    <div class="user-bar col-lg-6 col-md-6 col-sm-12">
      <ul class="user-menu">
        <li>
          <div class="select-agency-trigger-container">
            <a href="#" class="select-agency-trigger">Switch to other intranet</a>

            <div class="tooltip my-agency-tooltip">
              <span class="triangle"></span>
              <p>Please select your agency or public body</p>
            </div>
          </div>
        </li>
        <li class="hidden sign-in">
          <span class="separator">|</span>
          <a href="<?=site_url('/sign-in/')?>">Sign in</a>
        </li>
        <li class="hidden sign-out">
          <span class="separator">|</span>
          <a href="<?=wp_logout_url(get_permalink())?>">Sign out</a>
        </li>
        <li class="hidden register">
          <span class="separator">|</span>
          <a href="<?=site_url('/create-an-account/')?>">Register</a>
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
        <?php dynamic_sidebar('main-menu') ?>
      </div>
    </div>

    <?php if(Taggr::get_current() != 'homepage'): ?>
      <script data-name="header-my-moj" type="text/x-partial-template">
        <li class="category-item header-my-moj">
          <a class="category-link" href="">My MoJ <span class="arrow">▼</span></a>
          <?php $this->view('pages/homepage/my_moj/main', $my_moj) ?>
        </li>
      </script>
    <?php endif ?>
  </div>
</div>
