<?php

/*
 * Plugin Name: MOJ Auth
 * Plugin URI: https://github.com/ministryofjustice/intranet
 * Description: Plugin for authentication for the Intranet. It is a mu-plugin, so that it runs early in the page loading process. For now, it requires `firebase/php-jwt` & `league/oauth2-client` packages to be installed at the project root.
 * Author: Ministry of Justice - central-digital-product-team@digital.justice.gov.uk
 * Version: 0.0.1
 */

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

require_once 'jwt.php';
require_once 'oauth.php';
require_once 'utils.php';

/**
 * Class Auth
 * 
 * Handles authentication for the Intranet.
 * The class runs early in the page loading process.
 * As such, it should be lightweight, and not rely on WordPress functions.
 * 
 * @see https://github.com/firebase/php-jwt
 */

class Auth
{
    use AuthJwt;
    use AuthOauth;
    use AuthUtils;

    private $now    = null;
    private $debug  = false;
    private $https  = false;

    /**
     * Constructor
     * 
     * @param array $args optional Arguments (debug) to pass to the class.
     * @return void
     */

    public function __construct(array $args = [])
    {
        $this->now = time();
        $this->debug = $args['debug'] ?? false;
        $this->https = isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']);

        $this->initJwt();
        $this->initOauth();
    }

    /**
     * Handle the page request
     * 
     * This method is called on every page request. 
     * It checks the JWT cookie and the IP address to determine if the user should be allowed access.
     * 
     * @param string $required_role The necessary role required to access the page.
     * @return void
     */

    public function handlePageRequest(string $required_role = 'reader'): void
    {
        $this->log('handlePageRequest()');

        // If headers are already sent or we're doing a cron job, return early.
        if (\headers_sent() || defined('DOING_CRON')) {
            return;
        }

        // If we've hit the callback endpoint, then handle it here. On fail it exits with 401 & php code execution stops here.
        $access_token = 'callback' === $this->oauth_action ? $this->oauthCallback() : null;

        // The callback has returned an access token.
        if (is_object($access_token) && !$access_token->hasExpired()) {
            error_log('Access token is valid. Will set JWT.');
            // TODO save refresh token and other properties on the JWT.
            // Set a JWT cookie.
            $this->setJwt();
            // Get the origin request from the cookie.
            $user_redirect = \home_url($_COOKIE[$this::OAUTH_USER_URL_COOKIE_NAME] ?? '/');
            // Remove the cookie.
            $this->deleteCookie($this::OAUTH_USER_URL_COOKIE_NAME);
            // Redirect the user to the page they were trying to access.
            header('Location: ' . $user_redirect);
            exit();
        }

        // Get the JWT token from the request.
        $jwt = $this->getJwt();

        // Get the roles from the JWT and check that they're sufficient.
        $jwt_correct_role = $jwt && $jwt->roles ? in_array($required_role, $jwt->roles) : false;

        // Calculate the remaining time on the JWT token.
        $jwt_remaining_time = $jwt && $jwt->exp ? $jwt->exp - $this->now : 0;

        // JWT is valid and it's not time to refresh it.
        if ($jwt_correct_role && $jwt_remaining_time > $this::JWT_REFRESH) {
            return;
        }

        // There is no valid JWT, or it's about to expire.
        if ($this->ipAddressIsAllowed()) {
            // Set a JWT cookie.
            $this->setJwt();
            return;
        }

        // Refresh oAuth token if it's about to expire.


        // If there's any time left on the JWT then return.
        if ($jwt_remaining_time > 0) {
            return;
        }

        // Handle Azure AD/Entra ID OAuth. It redirects to Azure, php code execution always stops here.
        $this->oauthLogin();
    }

    /**
     * Log a user out.
     *
     * There is currently no UI machanism for logging out. This is here for completeness.
     * If it's used in the future it should used proceded with revoking CloudFront cookies.
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->deleteCookie($this::JWT_COOKIE_NAME);
        http_response_code(401);
        exit();
    }
}

$auth = new Auth(['debug' => true]);
$auth->handlePageRequest('reader');
