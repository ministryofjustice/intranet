<?php

namespace DeliciousBrains\WP_Offload_Media\Tweaks;

use Exception;

use function headers_sent;


/**
 * Amazon S3 and CloudFront - signing cookies.
 * 
 * This class contains functions related to running WP Offload Media with Amazon S3 and CloudFront.
 * 
 * To get to this point in code execution, the user has already been allowed access to the intranet's content,
 * this is either by IP address or authentication with Azure AD/Entra ID.
 * 
 * This class creates three cookies for CloudFront and sets them in the user's browser.
 * The cookies allow access to **all** CloudFront URLs, and subsequently the entire S3 bucket.
 * As all users have access to CloudFront, we don't need to generate & sign cookies for every user, we sign once and cache for a while.
 */

class AmazonS3AndCloudFrontSigning
{
    private ?int $now;
    private bool $https;
    private string $transient_key;

    private string|array|null $cloudfront_cookie_domain;
    private mixed $cloudfront_private_key;
    private mixed $cloudfront_host;
    private string $cloudfront_url;

    const CLOUDFRONT_DURATION = 60 * 15; // 15 minutes - important that this is at least nginx cache (10mins) + TRANSIENT_DURATION (2mins)
    const CLOUDFRONT_REFRESH = 60 * 5; // 5 minutes
    const TRANSIENT_DURATION = 60 * 2; // 2 minutes

    public function __construct()
    {
        $this->now = time();
        $this->https = isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']);

        // Cookie domain is important for sharing a cookie with a subdomain.
        $this->cloudfront_cookie_domain = preg_replace('/https?:\/\//', '', $_ENV['WP_HOME']);
        $this->cloudfront_private_key = $_ENV['AWS_CLOUDFRONT_PRIVATE_KEY'];
        $this->cloudfront_host =  $_ENV['AWS_CLOUDFRONT_HOST'];
        // Set the scheme/protocol for CloudFront, default to https.
        $cloudfront_scheme = isset($_ENV['AWS_CLOUDFRONT_SCHEME']) && $_ENV['AWS_CLOUDFRONT_SCHEME'] === 'http' ? 'http' : 'https';
        $this->cloudfront_url = $cloudfront_scheme . '://' . $this->cloudfront_host;

        // Create a transient key, unique to the scheme and host.
        $this->transient_key = "cloudfront_cookies_{$cloudfront_scheme}_" . str_replace([ ':', '/', '.',], '_', $this->cloudfront_host);

        // Clear AWS_CLOUDFRONT_PRIVATE_KEY from $_ENV global. It's not required elsewhere in the app.
        unset($_ENV['AWS_CLOUDFRONT_PRIVATE_KEY']);

        $this->handlePageRequest();

        add_filter('http_request_args', function ($args, $url) {
            // Send cookies with the request to the cdn.
            if (parse_url($url, PHP_URL_HOST) ===  $this->cloudfront_host) {
                $args['cookies'] = $this->getSignedCookie();
            }

            $request_is_for_self = str_starts_with($url, home_url());
            $auth_credentials_exist = !empty($_ENV['BASIC_AUTH_USER']) && !empty($_ENV['BASIC_AUTH_PASS']);

            if ($request_is_for_self && $auth_credentials_exist) {
                $args['headers']['Authorization'] = 'Basic ' . base64_encode($_ENV['BASIC_AUTH_USER'] . ':' . $_ENV['BASIC_AUTH_PASS']);
                error_log('ua=other: ' . $url);
                error_log(print_r($args, true));
            }

            return $args;
        }, 10, 2);
    }


    /**
     * Url safe base64 encode a string.
     * 
     * Replace unsafe characters +, = and / with the safe characters -, _ and ~.
     * Required for CloudFront cookies (and URLs).
     * 
     * @param string $value The string to encode.
     * @return string The encoded string.
     */

    public function urlSafeBase64Encode(string $value): string
    {
        return str_replace(
            ['+', '=', '/'],
            ['-', '_', '~'],
            base64_encode($value)
        );
    }

    /**
     * Url safe base64 decode a string.
     * 
     * Replace safe characters -, _ and ~ with the unsafe characters +, = and /.
     * Required for CloudFront cookies (and URLs).
     * 
     * @param string $value The string to decode.
     * @return string The decoded string.
     */

    public function urlSafeBase64Decode(string $value): string
    {
        return base64_decode(
            str_replace(
                ['-', '_', '~'],
                ['+', '=', '/'],
                $value
            )
        );
    }

    /**
     * Get the remaining time from the user's CloudFront cookie.
     * 
     * Use regex to parse the cookie and get the remaining time, it's faster than JSON parsing.
     * 
     * @return int The remaining time in seconds.
     */

    public function remainingTimeFromCookie(): int
    {
        $remaining_time = 0;

        try {
            $policy_base64 = $_COOKIE['CloudFront-Policy'] ?? null;

            if (!$policy_base64) {
                return $remaining_time;
            }

            preg_match('/"AWS:EpochTime":(\d+)}/', $this->urlSafeBase64Decode($policy_base64), $matches);
            $remaining_time =  isset($matches[1]) ? $matches[1] - $this->now : 0;
        } catch (Exception $e) {
            if (is_plugin_active('wp-sentry/wp-sentry.php')) {
                \Sentry\captureException($e);
            }

            // log the error to STDOUT
            error_log($e->getMessage());
        }

        return $remaining_time;
    }

    /**
     * Get the CloudFront public key ID.
     *
     * The public key ID is required for creating a signed cookie.
     * Having multiple public keys allows for key rotation.
     * This function parses an array of public key IDs and keys to find the correct key.
     *
     * @return string The CloudFront public key ID.
     * @throws Exception
     */

    public function getCloudfrontPublicKeyId(): string
    {
        // Get the private key.
        $private_key = openssl_get_privatekey($this->cloudfront_private_key);

        // Derive public key from a private key. It should be in the standard format (with a single newline at the end).
        $public_key_formatted = openssl_pkey_get_details($private_key)['key'];

        // Decode the JSON string to an array.
        $cloudfront_public_key_object = json_decode($_ENV['AWS_CLOUDFRONT_PUBLIC_KEYS_OBJECT'], true);

        // If the public key is not found, throw an exception.
        if (empty($cloudfront_public_key_object)) {
            throw new Exception('AWS_CLOUDFRONT_PUBLIC_KEYS_OBJECT was not found');
        }

        // Get the sha256 of the public key.
        $public_key_sha256 = hash('sha256', $public_key_formatted);

        // Get the first 8 characters of the sha256.
        $public_key_short = substr($public_key_sha256, 0, 8);

        // Find the matching array entry for the public key.
        $public_key_id_and_comment = array_filter($cloudfront_public_key_object, fn($key) => $key['comment'] === $public_key_short && !empty($key['id']));

        // If the public key is not found, throw an exception.
        if (empty($public_key_id_and_comment)) {
            throw new Exception('CloudFront public key not found');
        }

        return $public_key_id_and_comment[0]['id'];
    }

    /**
     * Create a signed cookie for CloudFront.
     *
     * @see https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/CreateURL_PHP.html AWS Documentation -
     *     Create a URL signature using PHP.
     * @see https://github.com/egunda/signed-cookie-php/blob/master/index.php Example implementation.
     *
     * @param string $url The URL to sign. Can have wildcards.
     *
     * @return array
     * @throws Exception
     */

    public function createSignedCookie(string $url): array
    {
        // Expire Time - this is for the policy. It's not the cookie expiry, i.e., when it's removed from the browser.
        $expiry = $this->now + $this::CLOUDFRONT_DURATION;

        $json = '{"Statement":[{"Resource":"' . $url . '","Condition":{"DateLessThan":{"AWS:EpochTime":' . $expiry . '}}}]}';

        $key = openssl_get_privatekey($this->cloudfront_private_key);

        if (!$key) {
            throw new Exception('Failed to load private key!');
        }

        if (!openssl_sign($json, $signed_policy, $key)) {
            throw new Exception('Failed to sign policy: ' . openssl_error_string());
        }

        // TEMP - for testing
        // $signed_url = $url . '?Expires=' . $expiry . '&Signature=' . $this->urlSafeBase64Encode($signed_policy) . '&Key-Pair-Id=' . $this->getCloudfrontPublicKeyId();

        return [
            "CloudFront-Key-Pair-Id" => $this->getCloudfrontPublicKeyId(),
            "CloudFront-Policy" => $this->urlSafeBase64Encode($json),
            "CloudFront-Signature" => $this->urlSafeBase64Encode($signed_policy)
        ];
    }

    /**
     * Get the signed cookie, with cache.
     * 
     * A wrapper function around createSignedCookie to get from cache if the value exists,
     * and save to cache after cookie creation.
     * 
     * @return array
     */

    public function getSignedCookie(): array
    {
        // Is there a signed cookie in the transient (cache)?
        $cached_cookies = get_transient($this->transient_key);

        if ($cached_cookies) {
            return $cached_cookies;
        }

        try {
            // Create a signed cookie for CloudFront with a suitable expiry time.
            $generated_cookies = $this->createSignedCookie($this->cloudfront_url . '/*');
            // Write the cookies to the cache.
            set_transient(
                $this->transient_key,
                $generated_cookies,
                $this::TRANSIENT_DURATION
            );
        } catch (Exception $e) {
            if (is_plugin_active('wp-sentry/wp-sentry.php')) {
                \Sentry\captureException($e);
            }
            error_log($e->getMessage());

            $generated_cookies = [];
        }

        return $generated_cookies;
    }

    /**
     * Handle the page request.
     * 
     * Timeline
     * 1. Does the user have a signed cookie with a long expiry in their browser?
     *    - Yes: Do nothing.
     *    - No: Continue.
     * 2. Get signed cookie (from cache or create).
     * 3. Set the CloudFront cookies in the user's browser.
     * 
     * @return void
     */

    public function handlePageRequest(): void
    {
        // If headers are already sent, or we're doing a cron job, return early.
        if (headers_sent() || defined('DOING_CRON')) {
            return;
        }

        // Clear the production session cookie - avoid sending conflicting cookies to CloudFront.
        $this->maybeClearProductionCookies();

        $remaining_time = $this->remainingTimeFromCookie();

        if ($remaining_time && $remaining_time > $this::CLOUDFRONT_REFRESH) {
            // Cookie-Policy exists, and it's not time to refresh it.
            return;
        }

        // If we're here, then we need to send a cookie to the user.

        $cookies = $this->getSignedCookie();

        // Properties for the cookies.
        $cloudfront_cookie_params = [
            'path=/',
            'HttpOnly',
            'Domain=' . $this->cloudfront_cookie_domain,
            'SameSite=Strict',
            ...($this->https ? ['Secure'] : []),
        ];
        $cloudfront_cookie_params_string = implode('; ', $cloudfront_cookie_params);

        // Set the cookies in the user's browser.
        foreach ($cookies as $name => $value) {
            // error_log(sprintf('Set-Cookie: %s=%s; %s', $name, $value, $cloudfront_cookie_params_string));
            header(sprintf('Set-Cookie: %s=%s; %s', $name, $value, $cloudfront_cookie_params_string), false);
        }
    }

    /**
     * Revoke the CloudFront cookies.
     * 
     * Delete the cookies from the user's browser.
     * 
     * @param ?string $domain Optional domain to revoke the cookies.
     * @return void
     */

    public function revoke(?string $domain): void
    {
        // If $domain is not passed in, default to the CloudFront cookie domain.
        $domain = $domain ?? $this->cloudfront_cookie_domain;

        // Properties for the cookies.
        $cloudfront_cookie_params = [
            'path=/',
            'HttpOnly',
            'Domain=' . $domain,
            'SameSite=Strict',
            'Expires=' . gmdate('D, d M Y H:i:s T', 0),
            ...($this->https ? ['Secure'] : []),
        ];
        $cloudfront_cookie_params_string = implode('; ', $cloudfront_cookie_params);

        // Delete the cookies.
        foreach (['CloudFront-Key-Pair-Id', 'CloudFront-Policy', 'CloudFront-Signature'] as $name) {
            header(sprintf('Set-Cookie: %s=; %s', $name, $cloudfront_cookie_params_string), false);
        }
    }

    /**
     * Clear the production session cookies.
     * 
     * If we are on a non-production environment e.g. dev or staging,
     * delete production CloudFront session cookie.
     * 
     * This is necessary, otherwise CDN requests will include the production 
     * session cookie. Resulting in 401 errors from the CloudFront.
     * 
     * @return void
     */

    public function maybeClearProductionCookies(): void
    {
        // Do nothing if we are on production.
        if ($_ENV['WP_ENV'] === 'production') {
            return;
        }

        // Does the home host start with dev, demo or staging?
        preg_match('/^(dev|demo|staging)\.(.*)/', parse_url($_ENV['WP_HOME'], PHP_URL_HOST), $matches);

        // If there is no match, return early.
        if (!$matches) {
            return;
        }

        // Delete production cookies - $matches[2] will be the production domain. 
        // e.g. if WP_HOME is dev.intranet.justice.gov.uk, it will be intranet.justice.gov.uk
        $this->revoke($matches[2]);
    }
}

new AmazonS3AndCloudFrontSigning();
