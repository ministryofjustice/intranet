<?php

/**
 * A dynamic 401 page.
 * 
 * We can either redirect to the login endpoint.
 * Or, if mamximum attempts have been used, then do nothing.
 */

namespace MOJ\Intranet;

// Exit if this file is included within a WordPress request.
if (defined('ABSPATH')) {
    error_log('moj-auth/401.php was accessed within the context of WordPress.');
    http_response_code(401) && exit();
}

define('DOING_STANDALONE_401', true);

$autoload = '../../../../vendor/autoload.php';

if (!file_exists($autoload)) {
    error_log('moj-auth/401.php autoloader.php was not found.');
    http_response_code(401) && exit();
}

require_once  $autoload;
require_once 'traits/jwt.php';
require_once 'traits/utils.php';

class Standalone401
{
    use AuthJwt;
    use AuthUtils;

    private $now            = null;
    private $debug          = false;
    private $https          = false;
    private $sub            = '';

    const OAUTH_LOGIN_URI      = '/auth/login';
    const MAX_FAILED_CALLBACKS = 3;
    const STATIC_401           = '../../themes/clarity/error-pages/401.html';
    const STATIC_401_REDIRECT  = '../../themes/clarity/error-pages/401-redirect.html';

    public function __construct(array $args = [])
    {
        $this->now = time();
        $this->debug = $args['debug'] ?? false;
        $this->https = isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']);

        if (!file_exists($this::STATIC_401)) {
            error_log('moj-auth/401.php ' . basename($this::STATIC_401) . ' was not found.');
            http_response_code(401) && exit();
        }

        if (!file_exists($this::STATIC_401_REDIRECT)) {
            error_log('moj-auth/401.php ' . basename($this::STATIC_401_REDIRECT) . ' was not found.');
            http_response_code(401) && exit();
        }

        if (empty($_ENV['WP_HOME'])) {
            error_log('moj-auth/401.php WP_HOME was not set.');
            http_response_code(401) && exit();
        }

        $this->initJwt();
    }

    public function handle401Request(): void
    {
        $this->log('handle401Request()');

        // Return early if it's a heartbeat request. 
        // If that endpoint is a 401 do nothing here.
        if ($_SERVER['REQUEST_URI'] === '/auth/heartbeat') {
            return;
        }

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt() ?: (object)[];

        $this->log('Request URI: ' . $_SERVER['REQUEST_URI']);

        // Always add the schema and domain here, to prevent an open redirect vulnerability.
        $jwt->success_url = $_ENV['WP_HOME'] . $_SERVER['REQUEST_URI'];

        // Set the cookie expiry to 0 to create a session cookie.
        $jwt->cookie_expiry = 0;

        // Set failed_callbacks with a default of 0.
        $jwt->failed_callbacks = isset($jwt->failed_callbacks) ? $jwt->failed_callbacks : 0;

        // Set a JWT without a role, to persist the user's ID, login attempts and success_url.
        $jwt = $this->setJwt($jwt);

        $this->log('handle401Request failed_callbacks: ' . $jwt->failed_callbacks);

        // The visitor has zero of a few failed callbacks.
        if ($jwt->failed_callbacks <= $this::MAX_FAILED_CALLBACKS) {

            // This template will redirect them to login.
            require_once $this::STATIC_401_REDIRECT;

            // Return early.
            return;
        }

        // The user has failed to login via the callback too many times, we won't redirect.
        require_once $this::STATIC_401;
    }
}

$standalone_401 = new Standalone401(['debug' => true]);
$standalone_401->handle401Request();
exit();
