<?php
function dw_set_cache_cookie() {

  $timeout = current_user_can('edit_posts')? 7 * DAYS_IN_SECONDS : 1;

  setcookie('disable_cache', 1, $timeout, COOKIEPATH, COOKIE_DOMAIN);

}
add_action('init', 'dw_set_cache_cookie', 10, 3);
