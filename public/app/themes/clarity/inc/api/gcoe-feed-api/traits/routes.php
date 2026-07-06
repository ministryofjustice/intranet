<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

trait Routes
{
    /**
     * Register the REST API routes for the Synergy feed.
     *
     * @return void
     */
    public function registerRoutes(): void
    {
        // Create an args varaible to be used for the feed and feed.csv routes.
        $feed_route_args = [
            'methods'  => 'GET',
            'callback' => [$this, 'getFeedCsv'],
            'permission_callback' => [__CLASS__, 'userHasPermission'],
        ];

        register_rest_route(
            'gcoe/v1',
            // URL is /wp-json/gcoe/v1/feed.csv
            '/feed.csv',
            $feed_route_args
        );
    }
}
