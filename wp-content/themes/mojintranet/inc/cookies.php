<?php

/**
 * Check if the user has permission to edit posts, and set or delete the
 * 'edit posts' cookie accordingly.
 *
 * Action: init
 */
function dw_set_cache_cookie() {
  if (current_user_can('edit_posts')) {
    dw_set_edit_posts_cookie(true);
  }
  elseif (isset($_COOKIE['dw_can_edit_posts'])) {
    dw_set_edit_posts_cookie(false);
  }
}
add_action('init', 'dw_set_cache_cookie', 10);

/**
 * Check if the user has permission to edit posts, and set or delete the
 * 'edit posts' cookie accordingly.
 *
 * This runs after wp_login, at which point current_user_can() cannot be used.
 *
 * Action: wp_login
 *
 * @param string $user_login
 * @param WP_User $user
 */
function dw_set_login_cookie($user_login, WP_User $user) {
  if (!empty($user->allcaps['edit_posts'])) {
    dw_set_edit_posts_cookie(true);
  }
  else {
    dw_set_edit_posts_cookie(false);
  }
}
add_action('wp_login', 'dw_set_login_cookie', 10, 2);

/**
 * Set or delete the 'edit posts' cookie.
 *
 * @param bool $active To set the cookie, or not to set the cookie. That is the question.
 */
function dw_set_edit_posts_cookie($active) {
  $cookie_url = preg_replace('#^https?://#', '', get_site_url());

  if ($active) {
    setcookie('dw_can_edit_posts', 1, strtotime('+7 days'), COOKIEPATH, $cookie_url);
  }
  else {
    setcookie('dw_can_edit_posts', 0, 1, COOKIEPATH, $cookie_url);
  }
}
