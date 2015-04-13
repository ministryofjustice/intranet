<?php if (!defined('ABSPATH')) die();

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
