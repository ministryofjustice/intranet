<?php if (!defined('ABSPATH')) die(); ?>

<div class="header" role="banner">
  <div class="grid skip-to-content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <a href="#content">Skip to main content</a>
    </div>
  </div>

  <div class="mobile-msg mobile-only">
    <p>&lt;Placeholder: mobile-only message explaining that we're working on the mobile design&gt;</p>
  </div>
  <div class="grid header-top">
    <div class="site-logo col-lg-6 col-md-6 col-sm-12">
      <a href="<?=WP_SITEURL?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
        <img src="<?=get_template_directory_uri()?>/assets/images/moj_logo.png" alt="Ministry of Justice logo" />
      </a>
    </div>
    <div class="user-bar col-lg-6 col-md-6 col-sm-12">
      <form class="my-intranet-form">
        <span class="department-dropdown-description">Intranet:</span>
        <a href="#" class="department-dropdown-trigger">
          <span class="label"></span>
          <span class="dropdown-icon">▼</span>
        </a>
        <ul class="department-list">
          <?php foreach($departments as $department): ?>
            <li class="agency" data-url="<?=$department['url']?>" data-department="<?=$department['name']?>" data-default="<?=$department['default']?>">
              <a href="#">
                <span class="department-icon"></span>
                <?=$department['label']?>
              </a>
            </li>
          <?php endforeach ?>
        </ul>

        <div class="tooltip my-agency-tooltip">
          <span class="triangle"></span>
          <p>Please select your agency or public body</p>
        </div>
      </form>
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
