<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="content-wrapper">
 *
 * @package WordPress
 */


// prevent clickjacking, advised by Context security review
header('X-Frame-Options: SAMEORIGIN');

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>
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

	<!--[if lte IE 8]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie8.css" type="text/css" media="screen" />
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



	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
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
		$gis = "general_intranet_track_homepage";
		$gistrackhome = get_option($gis);
		if ( is_front_page() || is_search() ){
			if ($gistrackhome == 1 || is_search() ){
				$gis = "general_intranet_google_tracking_code";
				$gisgtc = get_option($gis);
				echo $gisgtc;
			}
		}
		else {
			$gis = "general_intranet_google_tracking_code";
			$gisgtc = get_option($gis);
			echo $gisgtc;
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

	<div class="header">
    <div class="grid header-top">
      <div class="col-lg-8 col-md-8 col-sm-10">
        <div class="logo">
          <a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"  rel="home">
            <? if($gislogo): ?>
              <img src="<?=$gislogo?>" />
            <? endif ?>
            <?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>
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
      <div class="col-lg-4 col-md-4 col-sm-12 search-box">
        <div id='searchformdiv' class=''>
          <?php get_search_form(true); ?>
        </div>
      </div>
    </div>

    <div class="grid" class="header-bottom">
      <div id="mainnav">
        <div id="primarynav" role="navigation">
          <?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
          <?php
          wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
        </div>
      </div>

      <!--utility menu-->
      <div id="utilities" class="col-lg-4 col-md-4 col-sm-12 push-lg-8 push-md-8 mobile-hide">
        <?php if ( is_active_sidebar( 'utility-widget-area' ) ) : ?>
          <div id='utilitybar'>
            <ul class="menu">
              <?php dynamic_sidebar( 'utility-widget-area' ); ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div id="content" class="container main-content">
    <div class="content-wrapper">
