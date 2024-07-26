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
    private $login_attempts = null;
    private $success_uri    = null;

    const OAUTH_LOGIN_URI = '/auth/login';
    const MAX_AUTO_LOGIN_ATTEMPTS = 5;
    const STATIC_401 = '../../themes/clarity/error-pages/401.html';
    const REDIRECT_TEMPLATE = './templates/401-redirect.php';

    public function __construct(array $args = [])
    {
        $this->now = time();
        $this->debug = $args['debug'] ?? false;
        $this->https = isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']);
        $this->success_uri = $_SERVER['REQUEST_URI'];

        if (!file_exists($this::STATIC_401)) {
            error_log('moj-auth/401.php 401.html was not found.');
            http_response_code(401) && exit();
        }

        if (!file_exists($this::REDIRECT_TEMPLATE)) {
            error_log('moj-auth/401.php template was not found.');
            http_response_code(401) && exit();
        }

        $this->initJwt();
    }

    public function handle401Request(): void
    {
        $this->log('handle401Request()');

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt();

        // Set loginAttempts with a default of 1, or add one to the existing value.
        $this->login_attempts = empty($jwt->login_attempts) ? 1 : ((int) $jwt->login_attempts) + 1;

        // Set a JWT without a role, to persist the user's ID, login attempts and success_uri.
        $jwt = $this->setJwt();

        // Is this the first few times a visitor has hit the 401 page?
        if ($jwt->login_attempts <= $this::MAX_AUTO_LOGIN_ATTEMPTS) {

            // This template will redirect them to login.
            require_once $this::REDIRECT_TEMPLATE;

            // Return early.
            return;
        }

        // The user has hit a 401 too many times, we won't redirect.
        require_once $this::STATIC_401;
    }
}

$standalone_401 = new Standalone401(['debug' => true]);
$standalone_401->handle401Request();
exit();
