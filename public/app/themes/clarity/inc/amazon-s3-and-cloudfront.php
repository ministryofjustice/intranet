<?php

namespace DeliciousBrains\WP_Offload_Media\Tweaks;

/**
 * Amazon S3 and CloudFront tweaks.
 * 
 * A class for functions related to the Amazon S3 and CloudFront.
 * Functions related to cookie signing and Minio belong in the adjacent files.
 */

class AmazonS3AndCloudFrontTweaks
{

    public function __construct()
    {
        // Increase limits from 50 to 5000. 
        add_filter( 'as3cf_update_replace_provider_urls_batch_size', fn() => 5000);
        add_filter( 'as3cf_update_filter_post_excerpt_batch_size', fn() => 5000);
        add_filter( 'as3cf_update_fix_broken_item_extra_data_batch_size', fn() => 2500);

        // Increase limit from 50 to 750.
        add_filter( 'as3cf_update_as3cf_items_table_batch_size', fn() => 750);
        // 750 items take ~20 secs, so decrease interval from 2 to 1 minute.
        add_filter( 'as3cf_update_as3cf_items_table_interval', fn() => 1);
    }

}

new AmazonS3AndCloudFrontTweaks();
