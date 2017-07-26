<?php
// ---------------------------------------------
// Functions to improve the security of the site
// ---------------------------------------------

// Prevents WordPress from "guessing" URLs
function no_redirect_on_404($redirect_url)
{
    if (is_404()) {
        return false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'no_redirect_on_404');

// Disable xmlrpc
add_filter('xmlrpc_enabled', '__return_false');

// Disable pingbacls
function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');

// Remove autocomplete for password on wp-login.php
function acme_autocomplete_login_init()
{
    ob_start();
}
add_action('login_init', 'acme_autocomplete_login_init');
function acme_autocomplete_login_form()
{
    $content = ob_get_contents();
    ob_end_clean();

    $content = str_replace('id="loginform"', 'id="loginform" autocomplete="off"', $content);
    $content = str_replace('id="user_pass"', 'id="user_pass" autocomplete="off"', $content);

    echo $content;
}
add_action('login_form', 'acme_autocomplete_login_form');

// Force logout after x hours
function control_login_period( $expirein ) {
  return 180 * DAY_IN_SECONDS; // Cookies set to expire in 180 days.
}
add_filter( 'auth_cookie_expiration', 'control_login_period' );

// Allow editors to access theme options
function editor_theme_options() {
    $role_object = get_role( 'editor' );
    $role_object->add_cap( 'edit_theme_options' );
}
add_action('init','editor_theme_options');
