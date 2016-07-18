<?php
function dw_set_cache_cookie() {

  if(isset($_COOKIE['dw_can_edit_posts'])) {

    setcookie('dw_can_edit_posts', current_user_can('edit_posts') ? 1 : 0, current_user_can('edit_posts') ? 7 * DAYS_IN_SECONDS : 1, COOKIEPATH, COOKIE_DOMAIN);
  }

}
add_action('init', 'dw_set_cache_cookie', 10, 3);

function dw_login_set_admin_cookie($user_login, $user) {
  if (array_key_exists('edit_posts', $user->allcaps) && $user->allcaps['edit_posts'] == true) {
    setcookie('dw_can_edit_posts', 1,  7 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
  }
}
add_action('wp_login', 'dw_login_set_admin_cookie', 10, 2);
