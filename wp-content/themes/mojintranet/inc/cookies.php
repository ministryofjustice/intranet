<?php
function dw_set_cache_cookie() {
  if (current_user_can('edit_posts')) {
    setcookie('dw_can_edit_posts', 1, 7 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
  }
  elseif (isset($_COOKIE['dw_can_edit_posts'])) {
    setcookie('dw_can_edit_posts', 0, 1, COOKIEPATH, COOKIE_DOMAIN);
  }
}
add_action('init', 'dw_set_cache_cookie', 10, 3);
