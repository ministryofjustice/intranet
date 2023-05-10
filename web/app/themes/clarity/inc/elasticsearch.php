<?php
/**
 * ElasticPress; hooks and custom modification
 */

add_filter('ep_elasticsearch_version', fn($version) => '7.10');
add_filter('ep_host', fn($host) => env('EP_HOST'));


