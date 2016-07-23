<?php if (!defined('ABSPATH')) die();

if (!isset($cache_timeout)) $cache_timeout = 60;

header('X-Frame-Options: SAMEORIGIN');

if (!current_user_can('edit_posts') && $cache_timeout > 0) {
  header('Cache-Control: public, max-age=' . $cache_timeout);
  header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_timeout));
  header_remove("Pragma");
}
else {
  header('Cache-Control: private, max-age=0, no-cache');
  header("Pragma: no-cache");
  header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() - 60));
}
