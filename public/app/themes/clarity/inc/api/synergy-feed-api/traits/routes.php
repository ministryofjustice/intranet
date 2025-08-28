<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

use WP_Error;

trait Routes
{
    /**
     * Register the REST API routes for the Synergy feed.
     *
     * @return void
     */
    public function registerRoutes(): void
    {
        register_rest_route(
            'synergy/v1',
            'feeds',
            [
                'methods'  => 'GET',
                'callback' => fn() => $this->feeds_response,
                'permission_callback' => [$this, 'userHasPermission'],
            ]
        );

        // Create an args varaible to be used for the feed and feed.csv routes.
        $feed_route_args = [
            'methods'  => 'GET',
            'callback' => [$this, 'getFeedJson'],
            'permission_callback' => [$this, 'userHasPermission'],
            'validate_callback' => function ($request) {
                // Ensure the request is valid - look for an entry in BASE_URIS with the requested agency and content_type parameters.
                $base_uris = $this->getBaseUrisFromProperties(
                    $request->get_param('agency'),
                    $request->get_param('content_type')
                );

                // If no base URI is found, return a WP_Error with a 400 status code.
                if (empty($base_uris)) {
                    // If no base URI is found, return a WP_Error with a 400 status code.
                    return new WP_Error(
                        'invalid_agency_or_content_type',
                        'Invalid agency and content type combination provided.',
                        ['status' => 400]
                    );
                }

                // If we have a base URI, then the request is valid.
                return true;
            },
            'args' => [
                'agency' => [
                    'type'    => 'string',
                    'default' => 'hq',
                    'enum' => $this->agencies,
                ],
                'content_type' => [
                    'type'    => 'string',
                    'default' => 'hr',
                    'enum' => $this->content_types,
                ],
                'modified_after' => [
                    'type'    => 'string',
                    'required' => false,
                    'default' => '',
                    'validate_callback' => function ($param) {
                        if (empty($param)) {
                            return true; // If no value is provided, it's valid.
                        }
                        // If a value is provided, validate it.
                        return $this->isValidIsoDateTime($param);
                    },
                ],
                'format' => [
                    'type'    => 'string',
                    'default' => 'markdown',
                    'enum'    => ['html', 'markdown'],
                    // validate_callback is not needed here as 'enum' already validates the value.
                ]
            ],
        ];

        register_rest_route(
            'synergy/v1',
            // URL is /wp-json/synergy/v1/feed
            '/feed',
            $feed_route_args
        );

        $feed_route_args['callback'] = [$this, 'getFeedCsv'];

        register_rest_route(
            'synergy/v1',
            // URL is /wp-json/synergy/v1/feed.csv
            '/feed.csv',
            $feed_route_args
        );
    }
}
