<?php

namespace MOJ\Intranet;

// Exit if this file is included within a WordPress request.
if (defined('ABSPATH')) {
    error_log('moj-auth/verify.php was accessed within the context of WordPress.');
    http_response_code(401) && exit();
}

define('DOING_STANDALONE_VERIFY', true);

$autoload = '../../../../vendor/autoload.php';

if (!file_exists($autoload)) {
    error_log('moj-auth/verify.php autoloader.php was not found.');
    http_response_code(401) && exit();
}

require_once  $autoload;
require_once 'traits/jwt.php';
require_once 'traits/utils.php';

class StandaloneVerify
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

    public function handleAuthRequest(): void
    {
        $this->log('handleAuthRequest()');

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt();

        // Get the roles from the JWT and check that they're sufficient.
        $jwt_verified_role = $this->verifyJwtRoles($jwt?->roles ?? []);

        $status_code = $jwt_verified_role ? 200 : 401;

        // $status_code= 401;

        http_response_code($status_code) && exit();
    }
}
$debug = isset($_ENV['MOJ_AUTH_DEBUG']) && $_ENV['MOJ_AUTH_DEBUG'] === 'true';
$standalone_verify = new StandaloneVerify(['debug' => $debug]);
$standalone_verify->handleAuthRequest();
