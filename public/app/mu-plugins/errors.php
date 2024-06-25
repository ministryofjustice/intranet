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
 */

add_action('wp_error_added', function (string|int $code, string $message, mixed $data, WP_Error $wp_error) {
    if (is_array($message) || is_object($message)) {
        error_log("Error code: $code. Message: " . print_r($message, true));
    } else {
        error_log("Error code: $code. Message:  $message");
    }
}, 10, 4);
