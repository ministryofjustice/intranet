<?php
function dw_redirects() {
  $path = $_SERVER['REQUEST_URI'];

  //Search form -> search results page
  if(isset($_POST['s']) || $_POST['search-filter'] ) {
    $keywords = $_POST['s'] ?: '-';
    $keywords = rawurlencode(stripslashes($keywords));
    $keywords = str_replace('%2F', '%252F', $keywords);
    $keywords = str_replace('%5C', '%255C', $keywords);
    $filter = $_POST['search-filter'] ?: 'all';

    header('Location: ' . site_url() . '/search-results/' . $filter . '/' . $keywords . '/1/');
    exit;
  } elseif (preg_match('/\/search\/?$/',$path)) {
    header('Location: ' . site_url());
    exit;
  }

  if(strpos($path, 'guidance-and-support') || strpos($path, 'guidance-support')) {
    $new_path = preg_replace('/^([^?]*)(guidance-and-support|guidance-support)/', '${1}guidance', $path);
    if($new_path != $path) {
      header('Location: ' . site_url() . $new_path);
      exit;
    }
  }
}

function dw_rewrite_rules() {
  /**
  Remember!
  If you want "something/" to be accessible (i.e. with trailing slash), make sure to use "/?" in the rule.
  The reason why it is required is unknown, must be colliding with some other rule being applied behind the scenes.
   */

  //register url parameters
  add_rewrite_tag('%search-filter%', '([^&]+)');
  add_rewrite_tag('%search-string%', '([^&]+)');

  //News page
  $regex = '^newspage/page/([0-9]+)/(.*)';
  $redirect = 'index.php?page_id=' . get_page_by_path('newspage')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Blog page
  $regex = '^blog/page/([0-9]+)/(.*)';
  $redirect = 'index.php?page_id=' . get_page_by_path('blog')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Events page
  $regex = '^events/([0-9]+)(/.*)?';
  $redirect = 'index.php?page_id=' . get_page_by_path('events')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Search results page
  $regex = '^search-results/([^/]*)/([^/]*)/?';
  $redirect = 'index.php?page_id=' . get_page_by_path('search-results')->ID . '&search-filter=$matches[1]&search-string=$matches[2]';
  add_rewrite_rule($regex, $redirect, 'top');

  //Webchat archive page
  $regex = '^webchats/archive/?';
  $redirect = 'index.php?page_id=' . get_page_by_path('webchats/archive')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  // ping.json
  $regex = '^ping.json';
  $redirect = 'wp-content/themes/mojintranet/ping.php';
  add_rewrite_rule($regex, $redirect, 'top');

  $regex = '^service/(.*)';
  $redirect = 'index.php?controller=service&param_string=$matches[1]';
  add_rewrite_rule($regex, $redirect, 'top');

  //Custom controllers
  $regex = '^(password|create-an-account|sign-in)/?(.*)';
  $redirect = 'index.php?controller=$matches[1]&param_string=$matches[2]';
  add_rewrite_rule($regex, $redirect, 'top');
}
add_action('init', 'dw_redirects');
add_action('init', 'dw_rewrite_rules');

function redirect_404($template) {
  $error_template = locate_template( 'page_error.php' );
  if($error_template!='') {
    return $error_template;
  }
}
add_action('404_template','redirect_404',99);
