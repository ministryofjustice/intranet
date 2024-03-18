<?php

/**
 * We use ElasticPress to assist our search service
 * Moving our search to AWS OpenSearch caused the plugin to stop working
 * This hook fools ElasticPress into working.
 *
 * Further information:
 * https://elasticpress.zendesk.com/hc/en-us/articles/16677288265741-Compatibility
 */

add_filter(
    'ep_elasticsearch_version',
    function() {
        return '7.10';
    }
);
