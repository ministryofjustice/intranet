<?php

namespace MOJ\Intranet;

/**
 * Do not allow access outside WP, 401.php or verify.php
 * 
 * @used-by Auth
 * @used-by Standalone401
 * @used-by StandaloneVerify
 */

defined('ABSPATH') || defined('DOING_STANDALONE_401') || defined('DOING_STANDALONE_VERIFY') || exit;

/**
 * Util functions for MOJ\Intranet\Auth.
 */

trait AuthUtils
{
    /**
     * Log to the error log.
     * 
     * @param string $message The message to log.
     * @param mixed $data optional Any data to log.
     * @return void
     */

    public function log(string $message, $data = null, $level = 'debug'): void
    {
        // If this message is a debug level, and debug is turned off, then return. 
        if ($level === 'debug' && !$this->debug) {
            return;
        }

        error_log('MOJ_AUTH: ' . $message . ' ' . print_r($data, true));
    }

    /**
     * Hash a value using SHA256 and a salt.
     * 
     * @param string $value The value to hash.
     * @return string The hashed value.
     */

    public function hash(string $value): string
    {
        $this->log('hash()');

        return hash('sha256', $value  . $_ENV['AUTH_SALT']);
    }

    /**
     * A generic function to set a cookie.
     * 
     * We use SameSite=Lax policy because:
     * - We need the oauth cookies to ent to the server when the oauth provider redirects us to the callback url.
     * - We need the JWT to be sent to the server if visitors click links from outside of the intranet domain.
     * 
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $expiry The expiry time of the cookie. If not set, the cookie will expire at the end of the session.
     * 
     * @return void
     */

    public function setCookie(string $name, string $value, int $expiry = 0): void
    {
        $this->log('setCookie()');

        $cookie_parts = [
            $name . '=' . $value,
            'path=/',
            'HttpOnly',
            'SameSite=Lax',
            ...($this->https ? ['Secure'] : []),
            ...($expiry > 0 ? ['Expires=' . gmdate('D, d M Y H:i:s T', $expiry)] : []),
        ];

        // $this->log('setCookie()', $cookie_parts);

        header('Set-Cookie: ' . implode('; ', $cookie_parts), false);
    }

    /**
     * Delete a cookie by setting it to expire in the past.
     * 
     * @param string $name The name of the cookie to delete.
     */

    public function deleteCookie(string $name): void
    {
        $this->log('deleteCookie()');

        $this->setCookie($name, '', $this->now - 1);
    }

    /**
     * Find an item in an array.
     * 
     * When we upgrade to PHP 8.4, we can use array_any instead.
     * 
     * @param array $array
     * @param callable $callback
     * 
     * @return mixed
     */

    public function arrayAny($array, $callback)
    {
        foreach ($array as $entry) {
            if (call_user_func($callback, $entry) === true)
                return true;
        }
        return false;
    }

    /**
     * Ensure all items in an array satisfy the callback function.
     * 
     * When we upgrade to PHP 8.4, we can use array_all instead.
     * 
     * @param array $array
     * @param callable $callback
     * 
     * @return mixed
     */

    public function arrayAll($array, $callback)
    {
        foreach ($array as $entry) {
            if (call_user_func($callback, $entry) === false)
                return false;
        }
        return true;
    }
}
