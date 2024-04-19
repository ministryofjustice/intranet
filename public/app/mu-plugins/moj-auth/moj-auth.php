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

    private $now   = null;
    private $debug = false;
    private $https = false;
    private $sub   = '';

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

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt();

        // Set a JWT without a role, to persist the user's ID.
        if (!$jwt) {
            $jwt = $this->setJwt();
        }

        // If we've hit the callback endpoint, then handle it here. On fail it exits with 401 & php code execution stops here.
        $oauth_access_token = 'callback' === $this->oauth_action ? $this->oauthCallback() : null;

        // The callback has returned an access token.
        if (is_object($oauth_access_token) && !$oauth_access_token->hasExpired()) {
            $this->log('Access token is valid. Will set JWT and store refresh token.');
            // Set a JWT cookie.
            $this->setJwt([
                'expiry' => $oauth_access_token->getExpires(),
                'roles'  => ['reader']
            ]);
            // Store the tokens.
            $this->storeTokens($this->sub, $oauth_access_token, 'refresh');
            // Get the origin request from the cookie.
            $user_redirect = get_transient('oauth_user_url_' . $this->sub);
            // Remove the transient.
            delete_transient('oauth_user_url_' . $this->sub);
            // Redirect the user to the page they were trying to access.
            header('Location: ' . \home_url($user_redirect ?? '/'));
            exit();
        }

        // Get the roles from the JWT and check that they're sufficient.
        $jwt_correct_role = $jwt && $jwt->roles ? in_array($required_role, $jwt->roles) : false;

        // Calculate the remaining time on the JWT token.
        $jwt_remaining_time = $jwt && $jwt->exp ? $jwt->exp - $this->now : 0;

        // JWT is valid and it's not time to refresh it.
        if ($jwt_correct_role && $jwt_remaining_time > $this::JWT_REFRESH) {
            return;
        }

        /*
         * There is no valid JWT, or it's about to expire.
         */

        // If the IP address is allowed, set a JWT and return.
        if ($this->ipAddressIsAllowed()) {
            $this->setJwt(['roles' => ['reader']]);
            return;
        }

        // Refresh OAuth token if it's about to expire.
        $oauth_refresh_token = $this->sub ? $this->getStoredTokens($this->sub, 'refresh') : null;
        $oauth_refreshed_access_token = $oauth_refresh_token ? $this->oauthRefreshToken($oauth_refresh_token) : null;

        if (is_object($oauth_refreshed_access_token) && !$oauth_refreshed_access_token->hasExpired()) {
            $this->log('Refreshed access token is valid. Will set JWT and store refresh token.');
            // Set a JWT cookie.
            $jwt = $this->setJwt([
                'expiry' => $oauth_refreshed_access_token->getExpires(),
                'roles'  => ['reader']
            ]);
            // Store the tokens.
            $this->storeTokens($this->sub, $oauth_refreshed_access_token, 'refresh');
            return;
        }

        // If there's any time left on the JWT then return.
        if ($jwt_correct_role && $jwt_remaining_time > 0) {
            return;
        }

        // Handle Azure AD/Entra ID OAuth. It redirects to Azure or exits with 401 if disabled. php code execution always stops here.
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
        http_response_code(401) && exit();
    }
}

$auth = new Auth(['debug' => false]);
$auth->handlePageRequest('reader');
