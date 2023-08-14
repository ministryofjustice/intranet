<?php

function get_campaign_post_api($campaign_id): void
{
    get_campaign_api($campaign_id, 'posts');
}

add_action('wp_ajax_get_campaign_post_api', 'get_campaign_post_api');
add_action('wp_ajax_nopriv_get_campaign_post_api', 'get_campaign_post_api');
