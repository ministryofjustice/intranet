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


/**
 * Purge cache on save post
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function purge_on_save($post_id, $post, $update) {

  $purge_post_types = [
    'post',
    'page',
    'document',
    'event',
    'webchat',
    'news',
    'regional_news',
    'regional_page'
  ];

  $post_type = get_post_type($post_id);

  if (in_array($post_type, $purge_post_types) == false || $post->post_status != 'publish') return;

  $post_url = get_permalink($post_id);
  $purge_url = get_bloginfo('url') . '/purge-cache/main/' . base64_encode($post_url) . '/';

  wp_remote_post($purge_url,  ['blocking' => false, timeout => 1]);
}
add_action('save_post', 'purge_on_save', 999999, 3);

function ignore_user_abort_on_purge() {
  if(get_query_var('controller') == 'purge-cache') {
    ignore_user_abort(true);
  }
}
add_action('dw_redirect', 'ignore_user_abort_on_purge');
