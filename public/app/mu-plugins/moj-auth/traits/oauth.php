<?php

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

use Microsoft\Kiota\Authentication\PhpLeagueAccessTokenProvider;
use Microsoft\Kiota\Authentication\Cache\TransientAccessTokenCache;
use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Microsoft\Kiota\Authentication\Oauth\OnBehalfOfContext;

/**
 * OAuth functions for MOJ\Intranet\Auth.
 */

trait AuthOauth
{

    private $oauth_enabled    = true;
    private $oauth_tenant_id = '';
    private $oauth_authority  = '';
    private $oauth_app_id     = '';
    private $oauth_app_secret = '';
    private $oauth_scopes     = [];
    private $oauth_action     = '';

    const OAUTH_CALLBACK_URI         = '/auth/callback';
    const OAUTH_AUTHORIZE_ENDPOINT   = '/oauth2/v2.0/authorize';
    const OAUTH_TOKEN_ENDPOINT       = '/oauth2/v2.0/token';
    const OAUTH_STATE_COOKIE_NAME    = 'OAUTH_STATE';
    const OAUTH_USER_URL_COOKIE_NAME = 'OAUTH_USER_URL';

    public function initOauth()
    {
        $this->log('initOauth()');

        // Check for required environment variables. OAuth can be disable by not setting these.
        $this->oauth_enabled = !empty($_ENV['OAUTH_TENANT_ID']) && !empty($_ENV['OAUTH_CLIENT_ID']) && !empty($_ENV['OAUTH_CLIENT_SECRET']);

        if (!$this->oauth_enabled) {
            $this->log('Missing OAuth environment variables');
            return;
        }

        $this->oauth_tenant_id = $_ENV['OAUTH_TENANT_ID'];
        $this->oauth_authority  = 'https://login.microsoftonline.com/' . $this->oauth_tenant_id;
        $this->oauth_app_id     = $_ENV['OAUTH_CLIENT_ID'];
        $this->oauth_app_secret = $_ENV['OAUTH_CLIENT_SECRET'];
        $this->oauth_scopes     = [
            'User.Read',
            'offline_access' // To get a refresh token
        ];

        /**
         * Add openid to the scopes if the user is on edge on iOS.
         * 
         * Having openid in the scopes will force the user to use MFA,
         * successfully login and avoid the following error:
         * 
         * AADSTS50076: Due to a configuration change made by your administrator, 
         * or because you moved to a new location, you must use multi-factor 
         * authentication to access
         * 
         * TODO The long term plan is to *not* make an exception for Edge on iOS.
         * And, use the same scopes for all users. But, this will require further 
         * testing and comms to users.
         */
        if (isset($_SERVER['HTTP_USER_AGENT']) && str_contains($_SERVER['HTTP_USER_AGENT'], 'EdgiOS')) {
            $this->log('initOauth() Adding openid to the scopes for Edge on iOS');
            $this->oauth_scopes[] = 'openid';
        }

        if (
            isset($_SERVER['REQUEST_URI']) && str_starts_with ($_SERVER['REQUEST_URI'], '/auth/' )
        ) {
            $path = explode('?', $_SERVER['REQUEST_URI'])[0];
            $this->oauth_action = explode('/', $path )[2];
        }

        // Clear OAUTH_CLIENT_SECRET from $_ENV global. It's not required elsewhere in the app.
        unset($_ENV['OAUTH_CLIENT_SECRET']);
    }

    /**
     * Get OAuth client.
     * 
     * @return GenericProvider
     */

    public function getOAuthClient(): GenericProvider
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
            'pkceMethod'              => GenericProvider::PKCE_METHOD_S256
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

        if (!$this->oauth_enabled) {
            $this->log('OAuth is not enabled');
            http_response_code(401) && exit();
        }

        $oauth_client = $this->getOAuthClient();

        $authUrl = $oauth_client->getAuthorizationUrl();

        // Hash state (with a salt), else the user could read the state and make their cookie match the callback's state.
        $state_hashed =  $this->hash($oauth_client->getState());

        // Use a cookie to store oauth state.
        $this->setCookie($this::OAUTH_STATE_COOKIE_NAME, $state_hashed, -1);

        // Storing pkce prevents an attacker from potentially intercepting the auth code and using it.
        set_transient('oauth_pkce_' . $state_hashed, $oauth_client->getPkceCode(), 60 * 5); // 5 minutes

        header('Location: ' . $authUrl) && exit();
    }

    /**
     * Handle the OAuth callback.
     * 
     * This function will handle the OAuth callback and return the access token.
     * If the callback is invalid, it will return a 401 response.
     * 
     * @return AccessTokenInterface
     */

    public function oauthCallback(): AccessTokenInterface
    {
        $this->log('oauthCallback()');

        if (!$this->oauth_enabled) {
            $this->log('OAuth is not enabled');
            http_response_code(401) && exit();
        }

        if (!isset($_SERVER['REQUEST_URI']) || !str_starts_with($_SERVER['REQUEST_URI'], $this::OAUTH_CALLBACK_URI)) {
            $this->log('in oauthCallback(), request uri does not match');
            http_response_code(401) && exit();
        }

        // Get the hashed expected state from the cookie.
        $expected_state_hashed = $_COOKIE[$this::OAUTH_STATE_COOKIE_NAME] ?? null;
        // Delete the cookie.
        $this->deleteCookie($this::OAUTH_STATE_COOKIE_NAME);

        if (empty($expected_state_hashed)) {
            $this->log('No hashed expected state in the cookie.');
            http_response_code(401) && exit();
        }

        // Get the pkce code from the transient.
        $pkce = get_transient('oauth_pkce_' . $expected_state_hashed);
        // Delete the transient.
        delete_transient('oauth_pkce_' . $expected_state_hashed);

        // Check for state and code in the query params.
        if (!isset($_GET['state']) || !isset($_GET['code'])) {
            $this->log('No state or code in the query params');
            http_response_code(401) && exit();
        }

        if (empty($expected_state_hashed) || $expected_state_hashed !== $this->hash($_GET['state'])) {
            $this->log('Hashed states do not match');
            http_response_code(401) && exit();
        }

        // Initialize the OAuth client.
        $access_token = null;
        $oauth_client  = $this->getOAuthClient();

        try {
            // Set the pkce code.
            $oauth_client->setPkceCode($pkce);

            // Make the token request
            $access_token = $oauth_client->getAccessToken('authorization_code', ['code' => $_GET['code']]);
        } catch (IdentityProviderException $e) {
            $this->log('Error: ' . $e->getMessage(), null, 'error');
            $this->log('Error response body: ', $e->getResponseBody(), 'error');
            http_response_code(401) && exit();
        }

        return $access_token;
    }


    /**
     * Store the access and refresh tokens.
     * 
     * @param string $sub The subject of the tokens, i.e. a generated user ID.
     * @param AccessTokenInterface $access_token The access token object.
     * @param string|null $type The type of token to store. If not set, both access and refresh tokens will be stored.
     * @return void
     */

    public function storeTokens(string $sub, AccessTokenInterface $access_token, string|null $type = null): void
    {
        $this->log('storeTokens()');

        if (!$type ||  $type === 'access') {
            set_transient('access_token_' . $sub, $access_token->getToken(), $access_token->getExpires());
        }
        if (!$type ||  $type === 'refresh') {
            set_transient('refresh_token_' . $sub, $access_token->getRefreshToken(), $access_token->getExpires());
        }
    }

    /**
     * Get the stored tokens.
     * 
     * @param string $sub The subject of the tokens, i.e. a generated user ID.
     * @param string|null $type The type of token to get. If not set, both access and refresh tokens will be returned.
     * @return array|string|null
     */

    public function getStoredTokens(string $sub, string|null $type = null): array|string|null
    {
        $this->log('getStoredTokens()');

        if ($type === 'access') {
            return get_transient('access_token_' . $sub);
        }

        if ($type === 'refresh') {
            return get_transient('refresh_token_' . $sub);
        }

        return [
            'access' => get_transient('access_token_' . $sub),
            'refresh' => get_transient('refresh_token_' . $sub)
        ];
    }

    /**
     * Refresh the OAuth access token.
     * 
     * @param string $refresh_token The refresh token.
     * @return AccessTokenInterface
     */

    public function oauthRefreshToken(string $refresh_token): AccessTokenInterface
    {
        $this->log('oauthRefreshToken()');

        $oauth_client = $this->getOAuthClient();

        $access_token = $oauth_client->getAccessToken('refresh_token', [
            'refresh_token' => $refresh_token
        ]);

        return $access_token;
    }

    /**
     * Refresh the OAuth access token V2.
     * 
     * @param string $refresh_token The refresh token.
     * @return AccessTokenInterface
     */

    public function oauthRefreshTokenV2(string $entra_jwt)
    {
        $this->log('oauthRefreshToken()');

        // TODO: test this

        $tokenRequestContext = new OnBehalfOfContext(
            $this->oauth_tenant_id,
            $this->oauth_app_id,
            $this->oauth_app_secret,
            $entra_jwt
        );

        $allowedHosts = ['graph.microsoft.com'];

        $oauth_client = $this->getOAuthClient();

        $tokenProvider = new PhpLeagueAccessTokenProvider(
            $tokenRequestContext,
            $this->oauth_scopes,
            $allowedHosts,
            $oauth_client,
            new TransientAccessTokenCache($this->debug)
        );

        $tokenProvider->getOauthProvider()->setHttpClient(new \GuzzleHttp\Client());

        $authorizationToken = $tokenProvider->getAuthorizationTokenAsync('https://graph.microsoft.com')->wait();

        return $authorizationToken;
    }
}
