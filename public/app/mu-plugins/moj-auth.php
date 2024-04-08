<?php

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Class Auth
 * 
 * Handles authentication for the Intranet.
 * The class runs early in the page loading process.
 * As such, it should be lightweight, and not rely on WordPress functions.
 * 
 * @see https://github.com/firebase/php-jwt
 */

class Auth
{

    private $now = null;
    private $is_dev = false;

    // JWT
    private $jwt_secret = '';
    // Constants
    const JWT_ALGORITHM = 'HS256';
    const JWT_COOKIE_NAME = 'jwt';
    const JWT_DURATION = 60 * 60; // 1 hour
    const JWT_REFRESH = 60 * 5; // 5 minutes

    public function __construct()
    {
        $this->now = time();
        $this->is_dev = $_ENV['WP_ENV'] === 'development';
        $this->jwt_secret = $_ENV['JWT_SECRET'];

        // Clear JWT_SECRET from $_ENV global. It's not required elsewhere in the app.
        unset($_ENV['JWT_SECRET']);
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

        if (empty($_ENV['ALLOWED_IPS']) || empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }

        $newline_pattern  = '/\r\n|\n|\r/'; // Match newlines.
        $comments_pattern = '/\s*#.*/'; // Match comments.

        $allowedIps = array_map(
            'trim',
            preg_split($newline_pattern, preg_replace($comments_pattern, '', $_ENV['ALLOWED_IPS']))
        );

        error_log(print_r($allowedIps, true));

        return $this->ipMatch($_SERVER['REMOTE_ADDR'], $allowedIps);
    }

    /**
     * Get the JWT from the request.
     * 
     * @return bool|object Returns false if the JWT is not found or an object if it is found.
     */

    public function getJwt(): bool | object
    {
        // Get the JWT cookie from the request.
        $jwt = $_COOKIE[$this::JWT_COOKIE_NAME] ?? null;

        if (!is_string($jwt)) {
            return false;
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwt_secret, $this::JWT_ALGORITHM));
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            // TODO: remove this error_log once we confirm that this way of capturing to Sentry is working.
            error_log($e->getMessage());
            return false;
        }

        return $decoded;
    }

    /**
     * Set a JWT cookie.
     * 
     * @return void
     */

    public function setJwt(): void
    {

        $expiry = $this->now + $this::JWT_DURATION;

        $payload = [
            // Registered claims - https://datatracker.ietf.org/doc/html/rfc7519#section-4.1
            'exp' => $expiry,
            // Public claims - https://www.iana.org/assignments/jwt/jwt.xhtml
            'roles' => ['reader']
        ];

        $jwt = JWT::encode($payload, $this->jwt_secret, $this::JWT_ALGORITHM);

        // Build the cookie value - the the JWT cookie doesn't need to be accessed by the subdomains.
        $cookie_parts = [
            $this::JWT_COOKIE_NAME . '=' . $jwt,
            'path=/',
            'HttpOnly',
            'Expires=' . gmdate('D, d M Y H:i:s T', $expiry),
            'SameSite=Strict',
            ...($this->is_dev ? [] : ['Secure']),
        ];

        header('Set-Cookie: ' . implode('; ', $cookie_parts));
    }

    /**
     * Handle the page request
     * 
     * This method is called on every page request. 
     * It checks the JWT cookie and the IP address to determine if the user should be allowed access.
     * 
     * @param string $required_role The necessary role required to access the page.
     * @return void
     */

    public function handlePageRequest(string $required_role = 'reader'): void
    {
        // Get the JWT token from the request.
        $jwt = $this->getJwt();

        // Get the roles from the JWT and check that they're sufficient.
        $jwt_correct_role = $jwt && $jwt->roles ? in_array($required_role, $jwt->roles) : false;

        // Calculate the remaining time on the JWT token.
        $jwt_remaining_time = $jwt && $jwt->exp ? $jwt->exp - $this->now : 0;

        // JWT is valid and it's not time to refresh it.
        if ($jwt_correct_role && $jwt_remaining_time > $this::JWT_REFRESH) {
            return;
        }

        // There is no valid JWT, or it's about to expire.
        if ($this->ipAddressIsAllowed()) {
            // Set a JWT cookie.
            $this->setJwt();
            return;
        }

        // Here is a good place to handle Azure AD/Entra ID authentication.

        // If there's any time left on the JWT then return.
        if ($jwt_remaining_time > 0) {
            return;
        }

        // If the IP address is not allowed and the JWT has expired, then deny access.
        http_response_code(401);
        include(get_template_directory() . '/error-pages/401.html');
        exit();
    }
}

$auth = new Auth();
$auth->handlePageRequest('reader');
