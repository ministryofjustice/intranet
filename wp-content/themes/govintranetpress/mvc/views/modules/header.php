<?php if (!defined('ABSPATH')) die(); ?>

<div class="header" role="banner">
  <div class="grid skip-to-content-container">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <a href="#content">Skip to main content</a>
    </div>
  </div>
  <div class="grid header-top">
    <div class="col-lg-8 col-md-8 col-sm-10">
      <div class="site-logo">
        <a href="<?=WP_SITEURL?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
          <img src="<?=get_stylesheet_directory_uri()?>/images/moj_logo.png" alt="" />
        </a>
      </div>
    </div>

    <!-- mobile menu button -->
    <div class="col-sm-2 mobile-only">
      <div class="mobile-nav">
        <button type="button"></button>
      </div>
    </div>

    <!-- search box -->
    <div class="col-lg-4 col-md-4 col-sm-12">
      <?php if(
        !is_page_template('page-guidance-and-support-index.php') &&
        !is_page_template('search-results.php')):
      ?>
        <?php get_search_form(true); ?>
      <?php endif ?>
    </div>
  </div>

  <div class="grid header-bottom">
    <div id="mainnav" class="col-lg-8 col-md-8 col-sm-12">
      <nav id="primarynav" role="navigation">
        <?php if(!$is_moj_story) { ?>
          <?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
        <?php } else { ?>
          <?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'moj_story' ) ); ?>
        <?php } ?>
      </nav>
    </div>
  </div>
</div>
