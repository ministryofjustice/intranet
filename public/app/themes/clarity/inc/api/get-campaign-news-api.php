<?php

function get_campaign_news_api($campaign_id): void
{
    get_campaign_api($campaign_id, 'news');
}

add_action('wp_ajax_get_campaign_news_api', 'get_campaign_news_api');
add_action('wp_ajax_nopriv_get_campaign_news_api', 'get_campaign_news_api');
