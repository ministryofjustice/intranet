<?php if (!defined('ABSPATH')) die(); ?>

<title><?php wp_title('', true, 'right'); ?></title>

<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="stylesheet" type="text/css" media="all" href="<?=get_template_directory_uri()?>/css/style.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?=get_template_directory_uri()?>/css/fonts.css" />
<link rel="stylesheet" type="text/css" media="print" href="<?=get_template_directory_uri()?>/css/print.css" />

<!--[if lte IE 9]>
  <link rel="stylesheet" type="text/css" media="screen" href="<?=get_template_directory_uri()?>/css/ie.css?<?=add_checksum_param('css/ie.css')?>" />
<![endif]-->

<!--[if lt IE 9]>
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<![endif]-->
<!--[if gt IE 8]>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<![endif]-->

<!-- TODO the GA script to be optimised -->
<!--Google Analytics-->
<?php
  //write script for google analytics (only do on homepage if homepage tracking is set)
  $tracking_code = "<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '%s', 'auto');
    ga('send', 'pageview');

  </script>";
  $gis = "general_intranet_track_homepage";
  $gistrackhome = get_option($gis);
  if ( is_front_page() || is_search() ){
    if ($gistrackhome == 1 || is_search() ){
      $gis = "general_intranet_ga_id";
      $ga_id = get_option($gis);
      if($ga_id!=null) {
        echo sprintf($tracking_code, $ga_id);
      }
    }
  }
  else {
    $gis = "general_intranet_ga_id";
    $ga_id = get_option($gis);
    if($ga_id!=null) {
     echo sprintf($tracking_code, $ga_id);
    }
  }
  ?>


