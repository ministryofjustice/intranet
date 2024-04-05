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
    const JWT_ALGORITHM = 'HS256'; // The only algorithm supported in CloudFront functions.
    const JWT_COOKIE_NAME = 'jwt';
    const JWT_DOMAIN = 'intranet.docker';
    const JWT_DURATION = 60 * 60; // 1 hour
    const JWT_REFRESH = 60 * 5; // 5 minutes

    // CloudFront constants
    private $cloudfront_public_key_id = '';
    private $cloudfront_private_key = '';
    private $cloudfront_url = '';
    // Constants
    const CLOUDFRONT_COOKIE_DOMAIN = 'intranet.docker';
    const CLOUDFRONT_DURATION = 60 * 60; // 60 minutes

    public function __construct()
    {
        $this->now = time();
        if ($_ENV['WP_ENV'] === 'development') {
            $this->is_dev = true;
        }

        $this->jwt_secret = $_ENV['JWT_SECRET'];

        $this->cloudfront_public_key_id = $_ENV['CLOUDFRONT_PUBLIC_KEY_ID'];
        $this->cloudfront_private_key = $_ENV['CLOUDFRONT_PRIVATE_KEY'];
        // $this->cloudfront_url =  'http' . $this->is_dev ? '' : 's' . ' ://' . $_ENV['DELIVERY_DOMAIN'];
        $this->cloudfront_url =  'https://d33j2ssc6ogdaa.cloudfront.net';

        // Clear JWT_SECRET & CLOUDFRONT_PRIVATE_KEY from $_ENV global. 
        // They're not required elsewhere in the app.
        unset($_ENV['JWT_SECRET']);
        unset($_ENV['CLOUDFRONT_PRIVATE_KEY']);
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

        $allowedIps = array_map('trim', explode(',', $_ENV['ALLOWED_IPS']));

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
            'SameSite=Strict' // Will this work with subdomain?
        ];

        if ($_ENV['WP_ENV'] !== 'development') {
            $cookie_parts[] = 'Secure';
        }

        header('Set-Cookie: ' . implode('; ', $cookie_parts));
    }

    public function url_safe_base64_encode($value)
    {
        $encoded = base64_encode($value);
        // replace unsafe characters +, = and / with the safe characters -, _ and ~
        return str_replace(
            array('+', '=', '/'),
            array('-', '_', '~'),
            $encoded
        );
    }

    public function createSignedCookie($streamHostUrl, $resourceKey)
    {
        // @see https://github.com/egunda/signed-cookie-php/blob/master/index.php
        // @see https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/CreateURL_PHP.html

        $expiry = $this->now + $this::CLOUDFRONT_DURATION; // Expire Time

        $url = $streamHostUrl . '/' . $resourceKey; // Service URL

        $json = '{"Statement":[{"Resource":"' . $url . '","Condition":{"DateLessThan":{"AWS:EpochTime":' . $expiry . '}}}]}';

        $key = openssl_get_privatekey($this->cloudfront_private_key);
        if (!$key) {
            echo "<p>Failed to load private key!</p>";
            return;
        }
        if (!openssl_sign($json, $signed_policy, $key, OPENSSL_ALGO_SHA1)) {
            echo '<p>Failed to sign policy: ' . openssl_error_string() . '</p>';
            return;
        }

        // In case you want to use signed URL, just use the below code - make sure to pass a url path and not '*'.
        // $signedUrl = $url.'?Expires='.$expiry.'&Signature='.$this->url_safe_base64_encode($signed_policy).'&Key-Pair-Id='.$this->cloudfront_public_key_id;

        $signedCookies = [
            "CloudFront-Key-Pair-Id" => $this->cloudfront_public_key_id,
            "CloudFront-Policy" => $this->url_safe_base64_encode($json), //Canned Policy
            "CloudFront-Signature" => $this->url_safe_base64_encode($signed_policy)
        ];

        return $signedCookies;
    }

    public function setCloudFrontCookies()
    {

        $signedCookieCustomPolicy = $this->createSignedCookie($this->cloudfront_url, '*');

        $cookie_parts = [
            'path=/',
            'HttpOnly',
            'Domain=' . $this::CLOUDFRONT_COOKIE_DOMAIN,
            'SameSite=Strict',
            ...($this->is_dev ? [] : ['Secure']),
        ];

        $cookie_string = implode('; ', $cookie_parts);

        foreach ($signedCookieCustomPolicy as $name => $value) {
            // These cookies work if I copy and paste them into the browser console.
            // e.g. https://d33j2ssc6ogdaa.cloudfront.net/uploads/2024/02/09142406/287-4-150x150.jpg
            error_log(sprintf('Set-Cookie: %s=%s; %s', $name, $value, $cookie_string));
            header(sprintf('Set-Cookie: %s=%s; %s', $name, $value, $cookie_string), false);
        }

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
        error_log('Auth::handlePageRequest');

        $this->setCloudFrontCookies();

        return;

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
        http_response_code(403);
        include(get_template_directory() . '/error-pages/403.html');
        exit();
    }
}

$auth = new Auth();
$auth->handlePageRequest('reader');
