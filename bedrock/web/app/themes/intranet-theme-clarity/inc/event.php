<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}


/*
*
* This class generates event posts via the WP API
*
*/
class Event
{
    public function get_event_list($taxonomy, $tax_id = false)
    {
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();
        $siteurl = get_home_url();
        $agency_name = $activeAgency['wp_tag_id'];

        if ($taxonomy === 'search') {
            $response = wp_remote_get($siteurl.'/wp-json/intranet/v2/future-events/'.$agency_name);
        } elseif ($taxonomy === 'region') {
            $response = wp_remote_get($siteurl.'/wp-json/intranet/v2/region-events/'.$agency_name.'/'.$tax_id.'/');
        } elseif ($taxonomy === 'campaign') {
            $response = wp_remote_get($siteurl.'/wp-json/intranet/v2/campaign-events/'.$agency_name.'/'.$tax_id.'/');
        }

        if (is_wp_error($response)) {
            return;
        }

        $pagetotal = wp_remote_retrieve_header($response, 'x-wp-totalpages');
        $posts = json_decode(wp_remote_retrieve_body($response), true);
        $response_code = wp_remote_retrieve_response_code($response);
        $response_message = wp_remote_retrieve_response_message($response);

        if (200 == $response_code && $response_message == 'OK') {
            $event_list = $posts["events"];
            return $event_list;
        }
    }
}
