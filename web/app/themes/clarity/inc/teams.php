<?php

namespace MOJ\Intranet;

/**
 * Retrieves and returns guidance and form related data
 */
class Teams
{
    /**
     * 'http://127.0.0.1' is a temporary measure so that API calls do not get blocked by
     * changing IPs not whitelisted. All calls are within container.
     */
    public string $url = 'http://127.0.0.1';

    public function __construct(){}

    /**
     * Team News API
     *
     * @param
     * @return int|mixed|void
     */
    public function team_news_api($number)
    {
        return $this->remote_get('news', [
            'per_page' => $number,
            'page' => 1
        ]);
    }

    /**
     * Team Blog API
     *
     * @param
     * @return int|mixed|void
     */
    public function team_blog_api($number)
    {
        return $this->remote_get('blogs', [
            'per_page' => $number,
            'page' => 1
        ]);
    }

    /**
     * Team Events API
     *
     * @param
     * @return int|mixed|void
     */
    public function team_events_api($number)
    {
        return $this->remote_get('events', [
            'per_page' => $number,
            'page' => 1,
            'meta_key' => 'event-start-date', // orderby
            'order' => 'desc'
        ]);
    }

    /**
     * @param $type
     * @param $queries
     * @return int|mixed|void
     */
    public function remote_get($type, $queries)
    {
        $response = wp_remote_get($this->url . '/wp-json/wp/v2/team-' . $type . '/?' . http_build_query($queries));

        if (is_wp_error($response)) {
            return;
        }

        wp_remote_retrieve_header($response, 'x-wp-totalpages');
        $posts = json_decode(wp_remote_retrieve_body($response), true);
        $response_code = wp_remote_retrieve_response_code($response);
        $response_message = wp_remote_retrieve_response_message($response);

        return (200 == $response_code && $response_message == 'OK') ? $posts : 0;
    }
}
