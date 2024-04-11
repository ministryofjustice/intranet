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
        $this->minio_host = Config::get('AWS_S3_CUSTOM_HOST');

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

if(str_starts_with(Config::get('AWS_S3_CUSTOM_HOST'), 'minio')) {
    new AmazonS3AndCloudFrontForMinio();
}
