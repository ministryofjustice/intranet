<?php

namespace MOJ\Intranet;

// Exit if this file is included within a WordPress request.
if (defined('ABSPATH')) {
    error_log('moj-auth/standalone.php was accessed within the context of WordPress.');
    http_response_code(401) && exit();
}

define('DOING_STANDALONE_AUTH', true);

$autoload = '../../../../vendor/autoload.php';

if (!file_exists($autoload)) {
    error_log('moj-auth/standalone.php autoloader.php was not found.');
    http_response_code(401) && exit();
}

require_once  $autoload;
require_once 'jwt.php';
require_once 'utils.php';

class StandaloneAuth
{
    use AuthJwt;
    use AuthUtils;

    private $debug = false;
    private $sub   = '';

    public function __construct(array $args = [])
    {
        $this->debug = $args['debug'] ?? false;

        $this->initJwt();
    }

    public function handleAuthRequest(string $required_role = 'reader'): void
    {
        $this->log('handleAuthRequest()');

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt();

        // Get the roles from the JWT and check that they're sufficient.
        $jwt_correct_role = $jwt && $jwt->roles ? in_array($required_role, $jwt->roles) : false;

        $status_code = $jwt_correct_role ? 200 : 401;
        // $status_code = 401;
        $status_code = 200;

        http_response_code($status_code) && exit();
    }
}

$standalone_auth = new StandaloneAuth(['debug' => true]);
$standalone_auth->handleAuthRequest();
