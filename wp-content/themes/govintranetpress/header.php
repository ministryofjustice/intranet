<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="content-wrapper">
 *
 * @package WordPress
 */

class Page_header extends MVC_controller {
  function main() {
    $this->view('shared/beta_banner');
  }

  private function get_data() {
    return array(
    );
  }
}

// Are we in MOJ Story? Need to run early because of redirect
session_start();
$moj_slug = 'moj-story';
$full_site = $_GET['full_site']!==null?(boolean) $_GET['full_site']:null;

if($full_site !== null) { //use manual override
  $_SESSION['full_site'] = $full_site;
}
elseif(is_user_logged_in()) {
  $_SESSION['full_site'] = true;
}

if($full_site !== null) {
  $new_url = remove_query_arg('full_site');
  wp_redirect($new_url);
  die();
}

if ($_SESSION['full_site']) {
  $is_moj_story = false;
} else {
  if (has_ancestor($moj_slug) || $post->post_name==$moj_slug ) {
    $is_moj_story = true;
  } else {
    // wp_redirect( get_permalink_by_slug($moj_slug ), 302 );
    wp_redirect( site_url( '/about/moj-story' ), 302 ); // Hard coded as by function was too slow
    die;
  }
}

// prevent clickjacking, advised by Context security review
header('X-Frame-Options: SAMEORIGIN');

?><!DOCTYPE html>

<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<head data-application-url="<?=site_url()?>">
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php
		/*
		 * Print the <title> tag based on what is being viewed.
		 * We filter the output of wp_title() a bit -- see
		 * twentyten_filter_wp_title() in functions.php.
		 */
		wp_title( '', true, 'right' );

		?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />

  <!--[if lte IE 9]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie.css?<?=add_checksum_param('css/ie.css')?>" type="text/css" media="screen" />
	<![endif]-->
  <!--[if lte IE 7]>
		<script type="text/javascript" src="<?php bloginfo( 'template_url' ); ?>/js/json3.min.js"></script>
	<![endif]-->

	<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/prettyPhoto.css" rel="stylesheet">

	<!--[if (IE)&(lt IE 9) ]>
	        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
	<!--<![endif]-->
	<!--[if (IE)&(gt IE 8) ]>
	        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<!--<![endif]-->

	<!--[if lt IE 9]>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/html5-shiv.min.js"></script>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/respond.min.js"></script>
	<![endif]-->

	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>?<?=add_checksum_param('css/style.css')?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/print.css" />
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/custom.css" rel="stylesheet">

	<!-- [if lte IE 8]>
		<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/ie7/IE8.js"></script>
	<![endif]-->

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php
		/* We add some JavaScript to pages with the comment form
		 * to support sites with threaded comments (when in use).
		 */
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head();
	?>

	<style type='text/css'>
	/* Custom CSS rules below: */
	<?php

		$gis = "general_intranet_custom_css_code";
		$giscss = get_option($gis);
		echo $giscss;

		$gis = "general_intranet_enable_automatic_complementary_colour";
		$giscc = get_option($gis);

		// write custom css for background header colour

		$gis = "general_intranet_widget_border_height";
		$gisheight = get_option($gis);
		if (!$gisheight) $gisheight = 7;
		$gis = "general_intranet_header_background";
		$gishex = get_option($gis);
		$basecol=HTMLToRGB($gishex);
		$topborder = ChangeLuminosity($basecol, 33);

		//write custom css for logo
		$gis = "general_intranet_header_logo";
		$gisid = get_option($gis);
		$gislogow = wp_get_attachment_image_src( $gisid[0] );
		$gislogo = $gislogow[0] ;
		$gisw = $gislogow[1] + 10;

		$terms = get_terms('category');
	?>
	</style>
	<!--Google Analytics-->
	<?php
		//write script for google analytics (only do on homepage if homepage tracking is set)
    $tracking_code = "<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', '%s', 'auto');
      %s
      ga('send', 'pageview');

    </script>";
    $analytics_extra = "";
    // Add custom var for microsite tracking
    if ($is_moj_story) {
      $analytics_extra .= "ga('set', 'dimension1', 1);";
    }
		$gis = "general_intranet_track_homepage";
		$gistrackhome = get_option($gis);
		if ( is_front_page() || is_search() ){
			if ($gistrackhome == 1 || is_search() ){
				$gis = "general_intranet_ga_id";
				$ga_id = get_option($gis);
        if($ga_id!=null) {
				  echo sprintf($tracking_code, $ga_id, $analytics_extra);
        }
			}
		}
		else {
			$gis = "general_intranet_ga_id";
			$ga_id = get_option($gis);
      if($ga_id!=null) {
			 echo sprintf($tracking_code, $ga_id, $analytics_extra);
      }
		}
		?>

</head>

<?php
	$parentpageclass = (renderLeftNav("FALSE")) ? "parentpage" : "notparentpage";

	if ($govintranetpress_options['leftSubNav'] == "1" && is_page() ) { // check if left nav is on, on a page
		$leftnavflag = TRUE;
	}

?>

<body <?php body_class($parentpageclass); ?>>
  <?php // include(get_stylesheet_directory() . "/sidebar-cookiebar.php"); ?>

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
        <?php if($_SESSION['full_site']): ?>
          <?php get_search_form(true); ?>
        <?php endif ?>
      </div>
    </div>

    <div class="grid" class="header-bottom">
      <div id="mainnav" class="col-lg-8 col-md-8 col-sm-12">
        <nav id="primarynav" role="navigation">
          <?php if(!$is_moj_story) { ?>
            <?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
          <?php } else { ?>
            <?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'moj_story' ) ); ?>
          <?php } ?>
        </nav>
      </div>

      <!--utility menu-->
<!--      <div id="utilities" class="col-lg-4 col-md-4 col-sm-12 mobile-hide">
        <?php if ( is_active_sidebar( 'utility-widget-area' ) ) : ?>
          <div id='utilitybar'>
            <ul class="menu">
              <?php dynamic_sidebar( 'utility-widget-area' ); ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>-->
    </div>
  </div>

  <div id="content" class="container main-content" role="main">
    <div class="content-wrapper">


<?php

new Page_header();
