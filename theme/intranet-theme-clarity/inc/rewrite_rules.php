<?php

// function intranet_change_search_url_rewrite()
// {
//     if (is_search() && ! empty($_GET['s'])) {
//         wp_redirect(home_url('/') . urlencode(get_query_var('s')));
//         exit();
//     }
// }
// add_action('template_redirect', 'intranet_change_search_url_rewrite');

// function intranet_rw_rules()
// {
//     $regex = '^search=/([^/]*)/([^/]*)/?';
//     $redirect = 'index.php?page_id=' . get_page_by_path('s')->ID . '&search-filter=$matches[1]&search-string=$matches[2]';
//     add_rewrite_rule($regex, $redirect, 'top');
//
//     add_rewrite_tag('%search-filter%', '([^&]+)');
//     add_rewrite_tag('%search-string%', '([^&]+)');
// }
//
// add_action('init', 'intranet_rw_rules');
