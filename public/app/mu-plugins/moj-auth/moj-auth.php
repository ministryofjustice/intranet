<?php

/*
 * Plugin Name: MOJ Auth
 * Plugin URI: https://github.com/ministryofjustice/intranet
 * Description: Plugin for authentication for the Intranet. It is a mu-plugin, so that it runs early in the
 * page loading process. For now, it requires `firebase/php-jwt` & `league/oauth2-client` packages to be
 * installed at the project root.
 * Author: Ministry of Justice - central-digital-product-team@digital.justice.gov.uk
 *
 * Version: 0.0.1
 */

namespace MOJ\Intranet;

use Roots\WPConfig\Config;

// Do not allow access outside WP
defined('ABSPATH') || exit;

// If the plugin isn't enabled, return early.
if (Config::get('MOJ_AUTH_ENABLED') === false) {
    return;
}

require_once 'traits/jwt.php';
require_once 'traits/oauth.php';
require_once 'traits/utils.php';

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

    private $now            = null;
    private $debug          = false;
    private $https          = false;
    private $sub            = '';

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

    public function handleRequest(): void
    {
        $this->log('handleRequest()');

        if (!$this->oauth_action) {
            return;
        }

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt();

        // Set a JWT without a role, to persist the user's ID.
        if (!$jwt) {
            $jwt = $this->setJwt();
        }

        if ('login' === $this->oauth_action) {
            $this->handleLoginRequest();
            exit();
        }

        if ('callback' === $this->oauth_action) {
            $this->handleCallbackRequest();
            exit();
        }

        if ('heartbeat' === $this->oauth_action) {
            $this->handleHeartbeatRequest();
            exit();
        }

        if (!empty($this->oauth_action)) {
            $this->log('Unknown oauth action');
            exit();
        }
    }

    public function handleLoginRequest(): void
    {
        $this->log('handleLoginRequest()');

        // Handle Azure AD/Entra ID OAuth. It redirects to Azure or exits with 401 if disabled.
        $this->oauthLogin();
    }

    public function handleCallbackRequest(): void
    {
        $this->log('handleCallbackRequest()');

        // If we've hit the callback endpoint, then handle it here. On fail it exits with 401 & php code execution stops here.
        $oauth_access_token = $this->oauthCallback();

        // The callback has returned an access token.
        if (!is_object($oauth_access_token) || $oauth_access_token->hasExpired()) {
            $this->log('Access token is not valid, or expired.');

            // Update (or create) the JWT to keep track of failed callbacks.
            $jwt = $this->getJwt() ?: (object)[];

            // Set to 0 for a session cookie.
            $jwt->cookie_expiry = 0;

            // Set failed_callbacks with a default of 1, or add one to the existing value.
            $jwt->failed_callbacks = isset($jwt->failed_callbacks) ? ((int) $jwt->failed_callbacks) + 1 : 1;
            
            // Set the JWT.
            $this->setJwt($jwt);

            return;
        }

        $this->log('Access token is valid. Will set JWT and store refresh token.');

        $jwt = $this->getJwt() ?: (object)[];

        $jwt->expiry = $oauth_access_token->getExpires();

        $this->log('handleCallbackRequest initial token expiry: ' . $jwt->expiry);

        $jwt->roles = ['reader'];

        // Set a JWT cookie.
        $this->setJwt($jwt);

        // Store the tokens.
        $this->storeTokens($this->sub, $oauth_access_token, 'refresh');

        // Ensure we're redirecting to a page on the same domain as our home_url.
        if (!$jwt->success_url || !str_starts_with($jwt->success_url, home_url())) {
            $jwt->success_url = '/';
        }

        // Redirect the user to the page they were trying to access.
        header('Location: ' . $jwt->success_url) && exit();
    }

    public function handleHeartbeatRequest(): void
    {
        $this->log('handleHeartbeatRequest()');

        // Get the JWT token from the request. Do this early so that we populate $this->sub if it's known.
        $jwt = $this->getJwt();

        if (!$jwt) {
            return;
        }

        // Keep track of JWT mutations.
        $mutated_jwt = false;

        // Clear success_url & failed_callbacks here?
        if (isset($jwt->failed_callbacks) || !empty($jwt->success_url)) {
            $mutated_jwt = true;
            $jwt->failed_callbacks = null;
            $jwt->success_url = null;
        }

        // Calculate the remaining time on the JWT token.
        $jwt_remaining_time = $jwt && $jwt->exp ? $jwt->exp - $this->now : 0;

        // It's not time to refresh the JWT, and we need to update the JWT.
        if ($jwt_remaining_time > $this::JWT_REFRESH && $mutated_jwt) {
            $jwt = $this->setJwt($jwt);
        }

        // It's not time to refresh the JWT, return early.
        if ($jwt_remaining_time > $this::JWT_REFRESH) {
            return;
        }

        /*
         * The JWT is about to expire.
         */

        // Refresh OAuth token if it's about to expire.
        $oauth_refresh_token = $this->sub ? $this->getStoredTokens($this->sub, 'refresh') : null;
        $oauth_refreshed_access_token = $oauth_refresh_token ? $this->oauthRefreshToken($oauth_refresh_token) : null;

        if (is_object($oauth_refreshed_access_token) && !$oauth_refreshed_access_token->hasExpired()) {
            $this->log('Refreshed access token is valid. Will set JWT and store refresh token.');
            // Set a JWT cookie.
            $jwt->expiry = $oauth_refreshed_access_token->getExpires();
            $jwt->roles = ['reader'];
            $jwt = $this->setJwt($jwt);
            // Store the tokens.
            $this->storeTokens($this->sub, $oauth_refreshed_access_token, 'refresh');
        } else {
            $this->log('Refresh token was not valid.');
        }

    }

    /**
     * Log a user out.
     *
     * There is currently no UI mechanism for logging out. This is here for completeness.
     * If it's used in the future it should used proceeded with revoking CloudFront cookies.
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->deleteCookie($this::JWT_COOKIE_NAME);
        http_response_code(401) && exit();
    }
}


$auth = new Auth(['debug' => Config::get('MOJ_AUTH_DEBUG')]);
$auth->handleRequest();
