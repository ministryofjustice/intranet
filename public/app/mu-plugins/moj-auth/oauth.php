<?php

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

use League\OAuth2\Client\Provider\GenericProvider;

/**
 * OAuth functions for MOJ\Intranet\Auth.
 */

trait AuthOauth
{

    private $oauth_tennant_id = '';
    private $oauth_authority  = '';
    private $oauth_app_id     = '';
    private $oauth_app_secret = '';
    private $oauth_scopes     = [];
    private $oauth_action     = '';

    const OAUTH_CALLBACK_URI           = '/oauth2?action=callback';
    const OAUTH_AUTHORIZE_ENDPOINT     = '/oauth2/v2.0/authorize';
    const OAUTH_TOKEN_ENDPOINT         = '/oauth2/v2.0/token';
    const OAUTH_SESSION_ID_COOKIE_NAME = 'OAUTH_SESSION_ID';
    const OAUTH_USER_URL_COOKIE_NAME   = 'OAUTH_USER_URL';

    public function initOauth()
    {
        $this->log('initOauth()');

        $this->oauth_tennant_id = $_ENV['OAUTH_TENNANT_ID'];
        $this->oauth_authority  = 'https://login.microsoftonline.com/' . $this->oauth_tennant_id;
        $this->oauth_app_id     = $_ENV['OAUTH_CLIENT_ID'];
        $this->oauth_app_secret = $_ENV['OAUTH_CLIENT_SECRET'];
        $this->oauth_scopes     = [
            'api://' . $this->oauth_app_id . '/user_impersonation',
            'offline_access' // To get a refresh token
        ];
        if (
            isset($_SERVER['REQUEST_URI'])
            && str_starts_with($_SERVER['REQUEST_URI'], '/oauth2')
            && isset($_GET['action'])
            && in_array($_GET['action'], ['callback', 'login', 'logout'])
        ) {
            $this->oauth_action = $_GET['action'];
        }
    }

    /**
     * Get OAuth client.
     * 
     * @return \League\OAuth2\Client\Provider\GenericProvider
     */

    public function getOAuthClient(): \League\OAuth2\Client\Provider\GenericProvider
    {
        $this->log('getOAuthClient()');

        return new GenericProvider([
            'clientId'                => $this->oauth_app_id,
            'clientSecret'            => $this->oauth_app_secret,
            'redirectUri'             => \home_url($this::OAUTH_CALLBACK_URI),
            'urlAuthorize'            =>  $this->oauth_authority . $this::OAUTH_AUTHORIZE_ENDPOINT,
            'urlAccessToken'          =>  $this->oauth_authority . $this::OAUTH_TOKEN_ENDPOINT,
            'urlResourceOwnerDetails' => '',
            'scopes'                  => implode(' ', $this->oauth_scopes),
        ]);
    }

    /**
     * Handle the OAuth login.
     * 
     * @return void
     */

    public function oauthLogin(): void
    {
        $this->log('oauthLogin()');

        $oAuthClient = $this->getOAuthClient();

        $authUrl = $oAuthClient->getAuthorizationUrl();

        // Hash it with a salt, else the user could make their cookie match the callback's state. 
        $state_hashed = hash('sha256', $oAuthClient->getState() . $_ENV['AUTH_SALT']);

        // Use a cookie to store oauth state.
        // TODO rename OAUTH_SESSION_ID_COOKIE_NAME
        $this->setCookie($this::OAUTH_SESSION_ID_COOKIE_NAME, $state_hashed, -1);

        // Store the user's origin URL in a cookie.
        $this->setCookie($this::OAUTH_USER_URL_COOKIE_NAME, $_SERVER['REQUEST_URI'] ?? '', -1);

        header('Location: ' . $authUrl);
        exit();
    }

    public function oauthCallback(): \League\OAuth2\Client\Token\AccessTokenInterface
    {
        $this->log('oauthCallback()');

        if (!isset($_SERVER['REQUEST_URI']) || !str_starts_with($_SERVER['REQUEST_URI'], $this::OAUTH_CALLBACK_URI)) {
            error_log('in oauthCallback(), request uri does not match');
            http_response_code(401);
            exit();
        }

        $expected_state_hashed = $_COOKIE[$this::OAUTH_SESSION_ID_COOKIE_NAME] ?? null;

        // Remove the cookies.
        $this->deleteCookie($this::OAUTH_SESSION_ID_COOKIE_NAME);
        error_log('Removed the session cookie');
        // $this->setCookie($this::OAUTH_ORIGIN_COOKIE_NAME, '', $this->now -1);

        if (!isset($_GET['state']) || !isset($_GET['code'])) {
            // If there is no state or code in the query params,
            error_log('No state or code in the query params');
            http_response_code(401);
            exit();
        }

        $provided_state = $_GET['state'];

        $provided_state_hashed = hash('sha256', $provided_state . $_ENV['AUTH_SALT']);

        error_log($expected_state_hashed);
        error_log($provided_state_hashed);

        if (empty($expected_state_hashed)) {
            // If there is no expected state in the session,
            // do nothing and redirect to the home page.
            // header('Location: ' . $host . '/?type=error&message=Expected%20state%20not%20available');
            http_response_code(401);
            exit();
        }

        if (empty($provided_state_hashed) || $expected_state_hashed !== $provided_state_hashed) {
            error_log('State does not match');
            http_response_code(401);
            exit();

            // header('Location: ' . $host . '/auth.php?type=error&message=State%20does%20not%20match');
        }

        // Authorization code should be in the "code" query param
        $auth_code = $_GET['code'];

        // Initialize the OAuth client
        $oAuthClient = $this->getOAuthClient();

        $accessToken = null;
        try {
            // Make the token request
            $accessToken = $oAuthClient->getAccessToken('authorization_code', [
                'code' => $auth_code
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            error_log('Error: ' . $e->getMessage());
            http_response_code(401);
            exit();
            // header('Location: ' . $host . '/auth.php?type=error&message=' . urlencode($e->getMessage()));
        }


        return $accessToken;

        // $user = [];
        // if (null !== $accessToken) {
        //     // error_log(print_r($accessToken->expires, true));


        //     // We have an access token, which we may use in authenticated
        //     // requests against the service provider's API.
        //     error_log('Access Token: ' . $accessToken->getToken());
        //     error_log('Refresh Token: ' . $accessToken->getRefreshToken());
        //     error_log('Expired in: ' . $accessToken->getExpires());
        //     error_log('Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired'));
        // }
    }

}
