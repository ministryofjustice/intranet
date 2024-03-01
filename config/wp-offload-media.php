<?php

/**
 * WP Offload Media settings
 * @see https://deliciousbrains.com/wp-offload-media/doc/settings-constants
 */

use Roots\WPConfig\Config;
use function Env\env;

$as3_settings = array(
    // Storage Provider ('aws', 'do', 'gcp')
    'provider' => 'aws',
    // Use IAM Roles on Amazon Elastic Compute Cloud (EC2) or Google Compute Engine (GCE)
    'use-server-roles' => true,
    // Bucket to upload files to
    'bucket' => env('S3_BUCKET_NAME'),
    // Bucket region (e.g. 'us-west-1' - leave blank for default region)
    'region' => 'eu-west-2',
    // Automatically copy files to bucket on upload
    'copy-to-s3' => true,
    // Enable object prefix, useful if you use your bucket for other files
    'enable-object-prefix' => true,
    // Object prefix to use if 'enable-object-prefix' is 'true'
    'object-prefix' => 'media/',
    // Organize bucket files into YYYY/MM directories matching Media Library upload date
    'use-yearmonth-folders' => true,
    // Append a timestamped folder to path of files offloaded to bucket to avoid filename clashes and bust CDN cache if updated
    'object-versioning' => true,
    // Delivery Provider ('storage', 'aws', 'do', 'gcp', 'cloudflare', 'keycdn', 'stackpath', 'other')
    'delivery-provider' => env('CLOUDFRONT_URL') ? 'aws' : 'storage',
    // Rewrite file URLs to bucket (s3 or cloudfront)
    'serve-from-s3' => true,
    // Use a custom domain (CNAME), not supported when using 'storage' Delivery Provider
    'enable-delivery-domain' => !!env('CLOUDFRONT_URL'),
    // Custom domain (CNAME), not supported when using 'storage' Delivery Provider
    'delivery-domain' =>  env('CLOUDFRONT_URL'),
    // Enable signed URLs for Delivery Provider that uses separate key pair (currently only 'aws' supported, a.k.a. CloudFront)
    // 'enable-signed-urls' => false,
    // Access Key ID for signed URLs (aws only, replace '*')
    // 'signed-urls-key-id' => '********************',
    // Key File Path for signed URLs (aws only, absolute file path, not URL)
    // Make sure hidden from public website, i.e. outside site's document root.
    // 'signed-urls-key-file-path' => '/path/to/key/file.pem',
    // Private Prefix for signed URLs (aws only, relative directory, no wildcards)
    // 'signed-urls-object-prefix' => 'private/',
    // Serve files over HTTPS
    'force-https' => !!env('CLOUDFRONT_URL'),
    // Remove the local file version once offloaded to bucket
    'remove-local-file' => false,
    // Access Control List for the bucket
    'use-bucket-acls' => false,
);

// Merge in the access key and secret if they are set
if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY')) {
    unset($as3_settings['use-server-roles']);

    $as3_settings = array_merge($as3_settings, [
         // Access Key ID for Storage Provider (aws and do only, replace '*')
        'access-key-id' => env('AWS_ACCESS_KEY_ID'),
        // Secret Access Key for Storage Providers (aws and do only, replace '*')
        'secret-access-key' => env('AWS_SECRET_ACCESS_KEY'),
    ]);
}

Config::define('AS3CF_SETTINGS', serialize($as3_settings));
