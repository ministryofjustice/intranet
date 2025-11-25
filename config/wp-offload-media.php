<?php

/**
 * WP Offload Media settings
 *
 * @see https://deliciousbrains.com/wp-offload-media/doc/settings-constants
 */

use Roots\WPConfig\Config;
use function MOJ\Justice\env;

/**
 *  Setting                | Type    | Description
 *  -----------------------|---------|---------------------------------------------------------------------------
 *  provider               | string  | Storage Provider ('aws', 'do', 'gcp')
 *  use-server-roles       | boolean | Use IAM Roles on Amazon Elastic Compute Cloud
 *  bucket                 | string  | Bucket to upload files to
 *  region                 | string  | Bucket region (e.g. 'eu-west-2 string)
 *  copy-to-s3             | boolean | Automatically copy files to bucket on upload
 *  enable-object-prefix   | boolean | Enable object prefix, useful if bucket used for other files
 *  object-prefix          | string  | Object prefix to use if 'enable-object-prefix = 'true'
 *  use-yearmonth-folders  | boolean | Organize bucket files into YYYY/MM directories
 *  object-versioning      | boolean | Append a timestamp - avoids filename clashes
 *  delivery-provider      | string  | One of: storage, aws, do, gcp, cloudflare, keycdn, stackpath, other
 *  serve-from-s3          | boolean | Rewrite file URLs to bucket (s3 or cloudfront)
 *  enable-delivery-domain | string  | Use a custom domain (CNAME) - not supported when delivery-provider = storage
 *  delivery-domain        | string  | Custom domain (CNAME) - not supported when delivery-provider = storage
 *  force-https            | boolean | Serve files over HTTPS
 *  remove-local-file      | boolean | Remove the local file version once offloaded to bucket
 *  use-bucket-acls        | boolean | Access Control List for the bucket
 *  access-key-id          | string  | Access Key ID for Storage Provider (aws and do only, replace '*')
 *  secret-access-key      | string  | Secret Access Key for Storage Providers (aws and do only, replace '*')
 *
 */
$as3_settings = [
    'provider' => 'aws',
    'use-server-roles' => true,
    'bucket' => env('AWS_S3_BUCKET'),
    'region' => 'eu-west-2',
    'copy-to-s3' => true,
    'enable-object-prefix' => true,
    'object-prefix' => 'media/',
    'use-yearmonth-folders' => true,
    'object-versioning' => true,
    'delivery-provider' => env('AWS_CLOUDFRONT_HOST') ? 'aws' : 'storage',
    'serve-from-s3' => true,
    'enable-delivery-domain' => !!env('AWS_CLOUDFRONT_HOST'),
    'delivery-domain' => env('AWS_CLOUDFRONT_HOST'),
    'force-https' => env('AWS_CLOUDFRONT_SCHEME') !== 'http',
    'remove-local-file' => true,
    'use-bucket-acls' => false
];

/**
 * Manage storage access on different stacks.
 * 
 * Local development uses long-lived access keys and
 * secrets where stacks in CI/CD use server roles.
 */
if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY')) {
    unset($as3_settings['use-server-roles']);

    $as3_settings = array_merge($as3_settings, [
        'access-key-id' => env('AWS_ACCESS_KEY_ID'),
        'secret-access-key' => env('AWS_SECRET_ACCESS_KEY')
    ]);
}

Config::define('AS3CF_SETTINGS', serialize($as3_settings));
Config::define('AS3CFPRO_LICENCE', env('AS3CF_PRO_LICENCE') ?? '');
Config::define('AWS_S3_CUSTOM_HOST', env('AWS_S3_CUSTOM_HOST') ?? '');
Config::define('AWS_CLOUDFRONT_HOST', env('AWS_CLOUDFRONT_HOST') ?? '');
Config::define('AS3CF_SHOW_ADD_METADATA_TOOL', true);
