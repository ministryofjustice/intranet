<?php
function dw_set_cache_cookie() {

  $timeout = current_user_can('edit_posts')? 7 * DAYS_IN_SECONDS : 1;

  setcookie('dw_can_edit_posts', 1, $timeout, COOKIEPATH, COOKIE_DOMAIN);

}
add_action('init', 'dw_set_cache_cookie', 10, 3);
