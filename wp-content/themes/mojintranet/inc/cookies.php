<?php
function dw_set_cache_cookie() {
  if (current_user_can('edit_posts')) {
    dw_set_edit_posts_cookie(true);
  }
  elseif (isset($_COOKIE['dw_can_edit_posts'])) {
    dw_set_edit_posts_cookie(false);
  }
}
add_action('init', 'dw_set_cache_cookie', 10, 3);

function dw_set_login_cookie($user_login, $user) {
  if(isset($user->allcaps['edit_posts']) && $user->allcaps['edit_posts'] == true) {
    dw_set_edit_posts_cookie(true);
  }
}
add_action('wp_login', 'dw_set_login_cookie', 10, 2);

function dw_set_edit_posts_cookie($active) {
  $cookie_url = preg_replace('#^https?://#', '', get_site_url());

  if ($active == true) {
    setcookie('dw_can_edit_posts', 1, time() + (7 * DAY_IN_SECONDS), COOKIEPATH, $cookie_url);
  }
  else {
    setcookie('dw_can_edit_posts', 0, 1, COOKIEPATH, $cookie_url);
  }
}
