<?php

namespace MOJ\Intranet;

require_once dirname(__DIR__) . '../../vendor/autoload.php';

use GuzzleHttp;
use function Env\env;

/**
 * Metrics related to the service available at `/metrics/service`.
 *
 * The metrics available at this endpoint are served in OpenMetrics format.
 * It's a 'self-check' and includes metrics to ensure the service is running correctly.
 * e.g. pages are 401 when necessary.
 *
 * This file is outside of the WordPress application code, so that it will work even 
 * when WordPress encounters critical errors. e.g. unable to connect to the database.
 */

class Metrics
{
    // A url where where we are always expected to get a 200 response.
    const OPEN_URL = 'https://www.gov.uk';

    private $metrics_properties = [];

    private string $home_url;

    private GuzzleHttp\Client $guzzle_client;

    /**
     * Loads up actions that are called when WordPress initialises
     */
    public function __construct()
    {

        // Get the ip group of the incoming request.
        $ip_group = $_SERVER['HTTP_X_MOJ_IP_GROUP'] ?? 0;

        // Group 3 is Cloud Platform network, and group 4 is 127.0.0.1.
        // To test locally, set IPS_FORMATTED="0.0.0.0/0  3;"
        if (!in_array($ip_group, [3, 4])) {
            // Return early if IP is not allowed ranges.
            return;
        }

        $this->guzzle_client = new GuzzleHttp\Client();

        $this->home_url = env('WP_HOME');

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
                    $this->home_url,
                    ['headers' => ['X-Moj-Ip-Group' => 0]]
                ]
            ],
            'http_status_code_health' => [
                'help' => 'The http status code of /health.',
                'type' => 'gauge',
                'callback' => [$this, 'getStatusCode'],
                'args' => ["{$this->home_url}/health"]
            ],
            'http_status_code_wp_home' => [
                'help' => 'The http status code when accessing this service via it\'s full url as defined in WP_HOME.',
                'type' => 'gauge',
                'callback' => [$this, 'getStatusCode'],
                'args' => [$this->home_url]
            ]
        ];
    }

    /**
     * Get the status code from a url.
     *
     * @param string $url  Request url.
     * @param ?array $args Optional. Request arguments. Default empty array.
     *
     * @return int The response status code, or 0 on failure.
     */

    public function getStatusCode(string $url, ?array $request_args = []): int
    {
        // Default value.
        $status_code =  0;

        try {
            // Just make a http head request. We don't need get.
            $response = $this->guzzle_client->head($url, [
                'http_errors' => false,
                ...$request_args
            ]);
            $status_code = $response->getStatusCode();
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            // Check for an error. If there is, will return 0.
            error_log($e->getMessage());
        }

        return $status_code;
    }

    /**
     * Build up a string of metrics for this service and return it as a response.
     *
     * @return string
     */

    public function getServiceMetrics()
    {

        $response_string = '';

        foreach ($this->metrics_properties as $key => $value) {
            $response_string .= "# HELP {$key} {$value['help']}\n";
            $response_string .= "# TYPE {$key} {$value['type']}\n";
            $response_string .= "{$key} " . call_user_func($value['callback'], ...$value['args']) . "\n";
        }

        return $response_string;
    }

    /**
     * Serve the metrics and exit.
     *
     * @return void
     */

    public function serveMetrics(): void
    {
        header('Content-Type', 'text/plain');
        echo $this->getServiceMetrics();
        unset($this->guzzle_client);
        exit();
    }
}

$metrics = new Metrics();
$metrics->serveMetrics();
