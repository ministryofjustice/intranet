<?php if (!defined('ABSPATH')) die();

header('X-Frame-Options: SAMEORIGIN');

?>

<title><?php wp_title( '', true, 'right' ); ?></title>

<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!--[if lte IE 9]>
  <link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie.css?<?=add_checksum_param('css/ie.css')?>" type="text/css" media="screen" />
<![endif]-->

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>?<?=add_checksum_param('css/style.css')?>" />
<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/print.css" />

<!--[if (IE)&(lt IE 9) ]>
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<!--<![endif]-->
<!--[if (IE)&(gt IE 8) ]>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<!--<![endif]-->

<!--[if lte IE 7]>
  <script type="text/javascript" src="<?php bloginfo( 'template_url' ); ?>/js/json3.min.js"></script>
<![endif]-->
<!--[if lt IE 9]>
  <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/html5-shiv.min.js"></script>
  <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/respond.min.js"></script>
<![endif]-->

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

<?php wp_head(); ?>
