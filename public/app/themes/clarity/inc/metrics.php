<?php

namespace MOJ\Intranet;

use WP_REST_Response;

class Metrics
{
    // A url where where we are always expected to ger a 200 response.
    const OPEN_URL = 'https://www.justice.gov.uk';

    private $metrics_properties = [];

    /**
     * Loads up actions that are called when WordPress initialises
     */
    public function __construct()
    {
        // Get the ip group of the incoming request.
        $ip_group = $_SERVER['HTTP_X_MOJ_IP_GROUP'] ?? 0;

        // Group 3 is Cloud Platform network, and group 4 is 127.0.0.1.
        if (!in_array($ip_group, [3, 4])) {
            // Return early if IP is not allowed ranges.
            return;
        }

        // Define an array of metrics that we want to generate.
        $this->metrics_properties = [
            'http_status_code_control' => [
                'help' => 'The http status code when accessing an open site.',
                'type' => 'gauge',
                'callback' => [$this, 'getStatusCode'],
                'args' => [$this::OPEN_URL]
            ],
            'http_status_code_invalid_header' => [
                'help' => 'The http status code of when sending X-Moj-Ip-Group header.',
                'type' => 'gauge',
                'callback' => [$this, 'getStatusCode'],
                'args' => [
                    get_home_url(),
                    ['headers' => [
                        'X-Moj-Ip-Group' => 0
                    ]]
                ]
            ],
            'http_status_code_health' => [
                'help' => 'The http status code of /health.',
                'type' => 'gauge',
                'callback' => [$this, 'getStatusCode'],
                'args' => [
                    get_home_url(null, '/health'),
                    ['keep_home_url' => true]
                ]
            ],
            'http_status_code_wp_home' => [
                'help' => 'The http status code when accessing this service via it\'s full url as defined in WP_HOME.',
                'type' => 'gauge',
                'callback' => [$this, 'getStatusCode'],
                'args' => [
                    get_home_url(),
                    ['keep_home_url' => true]
                ]
            ]
        ];

        $this->actions();
    }

    /**
     * Register our rest endpoint.
     * 
     * @return void
     */
    public function actions(): void
    {
        add_action('rest_api_init', function () {
            register_rest_route('metrics/', '/service', [
                'methods' => 'GET',
                'callback' => [$this, 'getServiceMetrics'],
            ]);
        });

        // Allow metrics on endpoint: metrics/service
        add_action( 'init',  function() {
            add_rewrite_rule( 'metrics/service', 'index.php?rest_route=/metrics/service', 'top' );
        } );

        add_filter('rest_pre_serve_request', [$this, 'maybe_smg_feed'], 10, 4);
    }

    /**
     * Let us return non-json
     * 
     * @see https://wordpress.stackexchange.com/a/377954
     */

    function maybe_smg_feed($served, $result, $request, $server)
    {

        // Bail if the route of the current REST API request is not our custom route.
        if (
            '/metrics/service' !== $request->get_route() ||
            // Also check that the callback is smg_feed().
            [$this, 'getServiceMetrics'] !== $request->get_attributes()['callback']
        ) {

            return $served;
        }

        // Send headers.
        $server->send_header('Content-Type', 'text/text');

        // Echo the XML that's returned by smg_feed().
        echo $result->get_data();

        // And then exit.
        exit;
    }


    /**
     * Build up a string of metrics for this service and return it as a response.
     * 
     * @return WP_REST_Response
     */

    public function getServiceMetrics()
    {

        $response_string = '';

        foreach ($this->metrics_properties as $key => $value) {
            $response_string .= '# HELP ' . $value['help'] . "\n";
            $response_string .= '# TYPE ' . $value['type'] . "\n";
            $response_string .= $key . ' ' . call_user_func($value['callback'], ...$value['args'])  . "\n";
        }

        return $response_string;
    }

    /**
     * Get the status code from a url.
     * 
     * @param string $url request url
     * @param ?array  $args Optional. Request arguments. Default empty array.
     * @see WP_Http::request() for information on accepted arguments.
     * 
     * @return int|WP_Error The response or WP_Error on failure.
     */

    public function getStatusCode(string $url, ?array $request_args = []): int
    {
        // Just make a http head request. We don't need get.
        $head = wp_remote_head($url, $request_args);

        // Check for an error. If there is, return 0.
        return is_wp_error($head) ? 0 : (int) $head['response']['code'];
    }
}
