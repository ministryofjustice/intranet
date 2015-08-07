<?php
function dw_redirects() {
  //Search form -> search results page
  if(isset($_POST['s']) || $_POST['search-filter'] ) {
    $keywords = $_POST['s'] ?: '-';
    $keywords = rawurlencode(stripslashes($keywords));
    $keywords = str_replace('%2F', '%252F', $keywords);
    $keywords = str_replace('%5C', '%255C', $keywords);
    $filter = $_POST['search-filter'] ?: 'all';

    header('Location: ' . site_url() . '/search-results/' . $filter . '/' . $keywords . '/');
    exit;
  }

  $path = $_SERVER['REQUEST_URI'];
  if(strpos($path, 'guidance-and-support') || strpos($path, 'guidance-support')) {
    $new_path = str_replace(array('guidance-and-support','guidance-support'), 'guidance', $path);
    header('Location: ' . site_url() . $new_path);
    exit;
  }
}

function dw_rewrite_rules() {
  //News page
  $regex = '^newspage/page/([0-9]+)/(.*)';
  $redirect = 'index.php?page_id=' . get_page_by_path('newspage')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Search results page
  $regex = '^search-results/([^/]*)/([^/]*)/?';
  $redirect = 'index.php?page_id=' . get_page_by_path('search-results')->ID . '&search-filter=$matches[1]&search-string=$matches[2]';
  add_rewrite_rule($regex, $redirect, 'top');
  add_rewrite_tag('%search-filter%', '([^&]+)');
  add_rewrite_tag('%search-string%', '([^&]+)');

  // ping.json
  $regex = '^ping.json';
  $redirect = 'wp-content/themes/mojintranet/ping.php';
  add_rewrite_rule($regex, $redirect, 'top');
}
add_action('init', 'dw_redirects');
add_action('init', 'dw_rewrite_rules');

function redirect_404($template) {
  $error_template = locate_template( 'error.php' );
  if($error_template!='') {
    return $error_template;
  }
}
add_action('404_template','redirect_404',99);
