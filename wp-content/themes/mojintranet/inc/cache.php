<?php
  if(!is_user_logged_in()) {
    add_filter('wp_headers', 'dw_clear_headers');
  }

  function dw_clear_headers() {
    $cache_timeout = 90;
    
    $headers['Cache-Control'] = 'public, max-age='.$cache_timeout;
    $headers['Expires'] = gmdate('D, d M Y H:i:s \G\M\T', time() + ($cache_timeout?:60));

    return $headers;
  }
?>
