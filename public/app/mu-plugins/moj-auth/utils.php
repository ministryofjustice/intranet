<?php

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

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

    public function log(string $message, $data = null): void
    {
        if (!$this->debug) {
            return;
        }
        error_log($message . ' ' . print_r($data, true));
    }

    /**
     * Checks if a given IP address matches the specified CIDR subnet/s
     * 
     * @see https://gist.github.com/tott/7684443?permalink_comment_id=2108696#gistcomment-2108696
     * 
     * @param string $ip The IP address to check
     * @param mixed $cidrs The IP subnet (string) or subnets (array) in CIDR notation
     * @param string $match optional If provided, will contain the first matched IP subnet
     * @return boolean TRUE if the IP matches a given subnet or FALSE if it does not
     */

    public function ipMatch($ip, $cidrs, &$match = null): bool
    {
        $this->log('ipMatch()');

        foreach ((array) $cidrs as $cidr) {
            if (empty($cidr)) {
                continue;
            }
            $parts = explode('/', $cidr);
            $subnet = $parts[0];
            $mask = $parts[1] ?? 32;
            if (((ip2long($ip) & ($mask = ~((1 << (32 - $mask)) - 1))) == (ip2long($subnet) & $mask))) {
                $match = $cidr;
                return true;
            }
        }
        return false;
    }


    /**
     * Check if the IP address is allowed.
     * 
     * Checks that we have the environment variables ALLOWED_IPS and REMOTE_ADDR set.
     * Runs the ipMatch method to check if the REMOTE_ADDR is in the ALLOWED_IPS.
     * 
     * @return bool Returns true if the IP address is allowed, otherwise false.
     */

    public function ipAddressIsAllowed(): bool
    {
        $this->log('ipAddressIsAllowed()');

        if (empty($_ENV['ALLOWED_IPS']) || empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }

        $newline_pattern  = '/\r\n|\n|\r/'; // Match newlines.
        $comments_pattern = '/\s*#.*/'; // Match comments.

        $allowedIps = array_map(
            'trim',
            preg_split($newline_pattern, preg_replace($comments_pattern, '', $_ENV['ALLOWED_IPS']))
        );

        return $this->ipMatch($_SERVER['REMOTE_ADDR'], $allowedIps);
    }

    /**
     * A generic function to set a cookie.
     * 
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $expiry The expiry time of the cookie. If not set, the cookie will expire at the end of the session.
     * @return void
     */

    public function setCookie(string $name, string $value, int $expiry = 0): void
    {
        $this->log('setCookie()');

        $cookie_parts = [
            $name . '=' . $value,
            'path=/',
            'HttpOnly',
            'SameSite=Strict',
            ...($this->https ? ['Secure'] : []),
            ...($expiry > 0 ? ['Expires=' . gmdate('D, d M Y H:i:s T', $expiry)] : []),
        ];

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
}
