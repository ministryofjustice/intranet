<?php

namespace DeliciousBrains\WP_Offload_Media\Tweaks;

use Roots\WPConfig\Config;

/**
 * This class is cherry-picked functions from the wp-amazon-s3-and-cloudfront-tweaks plugin.
 * It is the config for using Minio Locally with WP Offload Media.
 * @see http://github.com/deliciousbrains/wp-amazon-s3-and-cloudfront-tweaks
 *
 * When accessing the WP Offload Media Lite setting page,
 * the plugin will log the following errors when trying to access the Minio server:
 * - AS3CF: Could not get Block All Public Access status: Error executing "GetPublicAccessBlock"
 * - AS3CF: Could not get Object Ownership status: Error executing "GetBucketOwnershipControls"
 * This is because Minio does not support these features.
 */

class AmazonS3AndCloudFrontForMinio
{

    // Define the Minio hostnames.
    private $minio_host = '';

    public function __construct()
    {
        /*
         * WP Offload Media & WP Offload Media Lite
         *
         * https://deliciousbrains.com/wp-offload-media/
         * https://wordpress.org/plugins/amazon-s3-and-cloudfront/
        */

        // If the S3_DOMAIN doesn't start with 'minio', then we are not using Minio.
        $this->minio_host = Config::get('S3_CUSTOM_DOMAIN');

        /*
         * Custom S3 API Example: Minio
         * @see https://min.io/
         */
        add_filter('as3cf_aws_s3_client_args', array($this, 'MinioS3ClientArgs'));
        add_filter('as3cf_aws_s3_url_domain', array($this, 'MinioS3UrlDomain'), 10, 5);
        add_filter('as3cf_aws_s3_console_url', array($this, 'MinioS3ConsoleUrl'));
        // The "prefix param" denotes what should be in the console URL before the path prefix value.
        // Minio just appends the path prefix directly after the bucket name.
        add_filter('as3cf_aws_s3_console_url_prefix_param', fn () => '/');

        /*
         * URL Rewrite related filters.
         */
        add_filter('as3cf_use_ssl', '__return_false', 10, 1);
    }

    /**
     * This filter allows you to adjust the arguments passed to the provider's service specific SDK client.
     *
     * The service specific SDK client is created from the initial provider SDK client, and inherits most of its config.
     * The service specific SDK client is re-created more often than the provider SDK client for specific scenarios, so if possible
     * set overrides in the provider client rather than service client for a slight improvement in performance.
     *
     * @see     https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.S3.S3Client.html#___construct
     * @see     https://docs.min.io/docs/how-to-use-aws-sdk-for-php-with-minio-server.html
     *
     * @handles `MinioS3ClientArgs`
     *
     * @param array $args
     *
     * @return array
     *
     * Note: A good place for changing 'signature_version', 'use_path_style_endpoint' etc. for specific bucket/object actions.
     */
    public function MinioS3ClientArgs($args)
    {
        // Example changes endpoint to connect to a local Minio server configured to use port 54321 (the default Minio port is 9000).
        $args['endpoint'] = 'http://' . $this->minio_host . ':9000';

        // Example forces SDK to use endpoint URLs with bucket name in path rather than domain name as required by Minio.
        $args['use_path_style_endpoint'] = true;

        return $args;
    }

    /**
     * This filter allows you to change the URL used for serving the files.
     *
     * @handles `MinioS3UrlDomain`
     *
     * @param string $domain
     * @param string $bucket
     * @param string $region
     * @param int    $expires
     * @param array  $args Allows you to specify custom URL settings
     *
     * @return string
     */
    public function MinioS3UrlDomain($domain, $bucket, $region, $expires, $args)
    {
        // Minio doesn't need a region prefix, and always puts the bucket in the path.
        return $this->minio_host . ':9000/' . $bucket;
    }



    /**
     * This filter allows you to change the base URL used to take you to the provider's console from WP Offload Media's settings.
     *
     * @handles `MinioS3ConsoleUrl`
     *
     * @param string $url
     *
     * @return string
     */
    public function MinioS3ConsoleUrl($url)
    {
        return 'http://' . $this->minio_host . ':9001/browser/';
    }
}

/**
 * Amazon S3 and CloudFront for Cloud Platform
 * 
 * This class contains functions related to running WP Offload Media with Amazon S3 and CloudFront.
 * 
 * To get to this point in code execution, the user has already been allowed access to the intranet's content,
 * this is either by IP address or authentication with Azure AD/Entra ID.
 * 
 * This class creates 3 cookies for CloudFront and sets them in the user's browser.
 * The cookies allow access to **all** CloudFront URLs, and subsequently the entire S3 bucket.
 * As all users have access to CloudFront, we don't need to generate & sign cookies for every user, we sign once and cache for a while.
 */

class AmazonS3AndCloudFrontForCloudPlatform
{

    private $now = null;
    private $is_dev = false;

    private $cloudfront_cookie_domain = '';
    private $cloudfront_private_key = '';
    private $cloudfront_url = '';

    const CLOUDFRONT_DURATION = 60 * 10; // 10 minutes
    const CLOUDFRONT_REFRESH = 60 * 5; // 5 minutes
    const TRANSIENT_DURATION = 60; // 1 minute
    const TRANSIENT_KEY = 'cloudfront_cookies';

    public function __construct()
    {
        $this->now = time();
        $this->is_dev = $_ENV['WP_ENV'] === 'development';

        // Cookie domain is important for sharing a cookie with a subdomain.
        $this->cloudfront_cookie_domain = preg_replace('/https?:\/\//', '', $_ENV['WP_HOME']);
        $this->cloudfront_private_key = $_ENV['CLOUDFRONT_PRIVATE_KEY'];
        $this->cloudfront_url =  'http' . $this->is_dev ? '' : 's' . ' ://' . $_ENV['DELIVERY_DOMAIN'];

        // Clear CLOUDFRONT_PRIVATE_KEY from $_ENV global. It's not required elsewhere in the app.
        unset($_ENV['CLOUDFRONT_PRIVATE_KEY']);

        $this->handlePageRequest();
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
            $policy = isset($_COOKIE['CloudFront-Policy']) ? $_COOKIE['CloudFront-Policy'] : null;

            if (!$policy) {
                return $remaining_time;
            }

            preg_match('/"AWS:EpochTime":(\d+)}/', $policy, $matches);
            $remaining_time =  isset($matches[1]) ? $matches[1] - $this->now : 0;
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            // TODO: possibly remove this error_log once we confirm that this way of capturing to Sentry is working.
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
     */

    public function getCloudfrontPublicKeyId(): string
    {
        // The first unique 8 chars of the public key are used to identify AWS's key id.
        $public_key_short = substr($_ENV['CLOUDFRONT_PUBLIC_KEY'], 71, 8);

        // Decode the JSON string to an array.
        $public_key_ids_and_keys = json_decode($_ENV['CLOUDFRONT_PUBLIC_KEY_OBJECT'], true);

        // Find the matching array entry for the public key.
        $public_key_id_and_key = array_filter($public_key_ids_and_keys, fn ($key) =>  $key['key'] === $public_key_short);

        // If the public key is not found, throw an exception.
        if (empty($public_key_id_and_key) || !$public_key_id_and_key[0]['id']) {
            throw new \Exception('CloudFront public key not found');
        }

        return $public_key_id_and_key[0]['id'];
    }

    /**
     * Create a signed cookie for CloudFront.
     * 
     * @see https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/CreateURL_PHP.html AWS Documentation - Create a URL signature using PHP.
     * @see https://github.com/egunda/signed-cookie-php/blob/master/index.php Example implementation.
     * 
     * @param string $url The URL to sign. Can have wildcards.
     * @return array
     */

    public function createSignedCookie(string $url)
    {

        // Expire Time - this is for the policy. It's not the cookie expiry, i.e. when it's removed from the browser.
        $expiry = $this->now + $this::CLOUDFRONT_DURATION;

        $json = '{"Statement":[{"Resource":"' . $url . '","Condition":{"DateLessThan":{"AWS:EpochTime":' . $expiry . '}}}]}';

        $key = openssl_get_privatekey($this->cloudfront_private_key);

        if (!$key) {
            throw new \Exception('Failed to load private key!');
        }

        if (!openssl_sign($json, $signed_policy, $key, OPENSSL_ALGO_SHA1)) {
            throw new \Exception('Failed to sign policy: ' . openssl_error_string());
        }

        return [
            "CloudFront-Key-Pair-Id" => $this->getCloudfrontPublicKeyId(),
            "CloudFront-Policy" => $this->urlSafeBase64Encode($json),
            "CloudFront-Signature" => $this->urlSafeBase64Encode($signed_policy)
        ];
    }

    /**
     * Handle the page request.
     * 
     * Timeline
     * 1. Does the user have a signed cookie with a long expiry in their browser?
     *   a. Yes: Do nothing.
     *   b. No: Continue.
     * 2. Is there a signed cookie in the transient (cache)?
     *   a. Yes: Get the cookie from the transient.
     *   b. No: Create a signed cookie for CloudFront with an expiry of 10mins, save it as a transient.
     * 3. Set the CloudFront cookies in the user's browser.
     * 
     * @return void
     */

    public function handlePageRequest(): void
    {

        $remaining_time = $this->remainingTimeFromCookie();

        if ($remaining_time && $remaining_time > $this::CLOUDFRONT_REFRESH) {
            // Cookie-Policy exists and it's not time to refresh it.
            return;
        }

        // If we're here then we need to send a cookie to the user.

        // Check the cache.
        $cached_cookies = get_transient($this::TRANSIENT_KEY);
        $generated_cookies = [];

        if (!$cached_cookies) {
            // Create a signed cookie for CloudFront.
            $generated_cookies = $this->createSignedCookie($this->cloudfront_url . '/*');

            // Write the cookies to the cache.
            set_transient(
                $this::TRANSIENT_KEY,
                $generated_cookies,
                $this::TRANSIENT_DURATION
            );
        }

        // Properties for the cookies.
        $cloudfront_cookie_params = [
            'path=/',
            'HttpOnly',
            'Domain=' . $this->cloudfront_cookie_domain,
            'SameSite=Strict',
            ...($this->is_dev ? [] : ['Secure']),
        ];
        $cloudfront_cookie_params_string = implode('; ', $cloudfront_cookie_params);

        // Set the cookies in the user's browser.
        foreach (($cached_cookies ?: $generated_cookies) as $name => $value) {
            // error_log(sprintf('Set-Cookie: %s=%s; %s', $name, $value, $cloudfront_cookie_params_string));
            header(sprintf('Set-Cookie: %s=%s; %s', $name, $value, $cloudfront_cookie_params_string), false);
        }
    }
}

/*
 * Run the appropriate class based on the S3_CUSTOM_DOMAIN.
 */

switch (str_starts_with(Config::get('S3_CUSTOM_DOMAIN'), 'minio')) {
    case true:
        new AmazonS3AndCloudFrontForMinio();
        break;
    default:
        new AmazonS3AndCloudFrontForCloudPlatform();
        break;
}
