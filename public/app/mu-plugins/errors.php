<?php

/**
 * Errors
 * Only allowed in development environment.
 */

defined('ABSPATH') || exit;

if (getenv('WP_ENV') !== 'development') {
    return;
}

/**
 * wp_error_added
 * When WP_Error is called, it is up to the developer to access and handle the error, they are not logged by default.
 * This action/function ensures that any errors added to WP_Error will be logged immediately.
 *
 * REST requests are excluded: core routinely constructs WP_Error objects it never returns
 * (e.g. WP_REST_Users_Controller::get_user() builds one before validating the id), so logging
 * at construction floods the log with false positives. REST errors are instead logged below,
 * via rest_request_after_callbacks, only when an endpoint actually returns one.
 */

add_action('wp_error_added', function (string|int $code, string $message, mixed $data, WP_Error $wp_error) {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }
    if (is_array($data) && !empty($data['skip-log'])) {
        return;
    }
    if (is_array($message) || is_object($message)) {
        error_log("Error code: $code. Message: " . print_r($message, true));
    } else {
        error_log("Error code: $code. Message:  $message");
    }

}, 10, 4);

/**
 * rest_request_after_callbacks
 * Fires after a REST route's permission check and callback have run. $response is the
 * WP_Error the endpoint actually returned (not one it created and discarded), so this
 * logs only genuine REST error outcomes. Note: requests short-circuited earlier, e.g. by
 * rest_authentication_errors, do not reach this filter and are intentionally not logged.
 */

add_filter('rest_request_after_callbacks', function ($response, $handler, $request) {
    if (!is_wp_error($response)) {
        return $response;
    }
    foreach ($response->get_error_codes() as $code) {
        $data = $response->get_error_data($code);
        if (is_array($data) && !empty($data['skip-log'])) {
            continue;
        }
        $message = $response->get_error_message($code);
        error_log("Error code: $code. Message:  $message Route: " . $request->get_route());
    }
    return $response;
}, 10, 3);
