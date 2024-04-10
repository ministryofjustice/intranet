<?php

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use League\OAuth2\Client\Provider\GenericProvider;

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

    private $debug  = false;
    private $now    = null;
    private $is_dev = false;
    private $https  = false;

    // JWT
    private $jwt_secret = '';
    // Constants
    const JWT_ALGORITHM   = 'HS256';
    const JWT_COOKIE_NAME = 'jwt';
    const JWT_DURATION    = 60 * 60; // 1 hour
    const JWT_REFRESH     = 60 * 5; // 5 minutes

    // OAuth
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

    public function __construct(array $args = [])
    {
        $this->debug = $args['debug'] ?? false;
        $this->now = time();
        $this->is_dev = $_ENV['WP_ENV'] === 'development';
        $this->https = isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']);
        $this->jwt_secret = $_ENV['JWT_SECRET'];

        // Clear JWT_SECRET from $_ENV global. It's not required elsewhere in the app.
        unset($_ENV['JWT_SECRET']);

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
            && in_array($_GET['action'], ['callback', 'login'])
        ) {
            $this->oauth_action = $_GET['action'];
        }
    }

    /**
     * Log to the error log.
     * 
     * @param string $message The message to log.
     * @param mixed $data optional Any data to log.
     * @return void
     */

    public function log(string $message, $data = null): void
    {
        if (!$this->debug) {
            return;
        }
        error_log($message . ' ' . print_r($data, true));
    }


    /**
     * Checks if a given IP address matches the specified CIDR subnet/s
     * 
     * @see https://gist.github.com/tott/7684443?permalink_comment_id=2108696#gistcomment-2108696
     * 
     * @param string $ip The IP address to check
     * @param mixed $cidrs The IP subnet (string) or subnets (array) in CIDR notation
     * @param string $match optional If provided, will contain the first matched IP subnet
     * @return boolean TRUE if the IP matches a given subnet or FALSE if it does not
     */

    public function ipMatch($ip, $cidrs, &$match = null): bool
    {
        $this->log('ipMatch()');

        foreach ((array) $cidrs as $cidr) {
            if (empty($cidr)) {
                continue;
            }
            $parts = explode('/', $cidr);
            $subnet = $parts[0];
            $mask = $parts[1] ?? 32;
            if (((ip2long($ip) & ($mask = ~((1 << (32 - $mask)) - 1))) == (ip2long($subnet) & $mask))) {
                $match = $cidr;
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the IP address is allowed.
     * 
     * Checks that we have the environment variables ALLOWED_IPS and REMOTE_ADDR set.
     * Runs the ipMatch method to check if the REMOTE_ADDR is in the ALLOWED_IPS.
     * 
     * @return bool Returns true if the IP address is allowed, otherwise false.
     */

    public function ipAddressIsAllowed(): bool
    {
        $this->log('ipAddressIsAllowed()');

        if (empty($_ENV['ALLOWED_IPS']) || empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }

        $newline_pattern  = '/\r\n|\n|\r/'; // Match newlines.
        $comments_pattern = '/\s*#.*/'; // Match comments.

        $allowedIps = array_map(
            'trim',
            preg_split($newline_pattern, preg_replace($comments_pattern, '', $_ENV['ALLOWED_IPS']))
        );

        return $this->ipMatch($_SERVER['REMOTE_ADDR'], $allowedIps);
    }


    public function setCookie(string $name, string $value, int $expiry = 0): void
    {
        $this->log('setCookie()');

        $cookie_parts = [
            $name . '=' . $value,
            'path=/',
            'HttpOnly',
            'SameSite=Strict',
            ...($this->is_dev ? [] : ['Secure']),
            ...($expiry > 0 ? ['Expires=' . gmdate('D, d M Y H:i:s T', $expiry)] : []),
        ];

        header('Set-Cookie: ' . implode('; ', $cookie_parts), false);
    }

    public function deleteCookie(string $name): void
    {
        $this->log('deleteCookie()');

        $this->setCookie($name, '', $this->now - 1);
    }

    /**
     * Get the JWT from the request.
     * 
     * @return bool|object Returns false if the JWT is not found or an object if it is found.
     */

    public function getJwt(): bool | object
    {
        $this->log('getJwt()');

        // Get the JWT cookie from the request.
        $jwt = $_COOKIE[$this::JWT_COOKIE_NAME] ?? null;

        if (!is_string($jwt)) {
            return false;
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwt_secret, $this::JWT_ALGORITHM));
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            // TODO: remove this error_log once we confirm that this way of capturing to Sentry is working.
            error_log($e->getMessage());
            return false;
        }

        return $decoded;
    }

    /**
     * Set a JWT cookie.
     * 
     * @return void
     */

    public function setJwt(): void
    {
        $this->log('setJwt()');

        $expiry = $this->now + $this::JWT_DURATION;

        $payload = [
            // Registered claims - https://datatracker.ietf.org/doc/html/rfc7519#section-4.1
            'exp' => $expiry,
            // Public claims - https://www.iana.org/assignments/jwt/jwt.xhtml
            'roles' => ['reader']
        ];

        $jwt = JWT::encode($payload, $this->jwt_secret, $this::JWT_ALGORITHM);

        $this->setCookie($this::JWT_COOKIE_NAME, $jwt, $expiry);
    }



    /**
     * Handle the OAuth login.
     * 
     * @return void
     */

    public function oauthLogin(): void
    {
        $this->log('oauthLogin()');

        $oAuthClient = new GenericProvider([
            'clientId'                => $this->oauth_app_id,
            'clientSecret'            => $this->oauth_app_secret,
            'redirectUri'             => \home_url($this::OAUTH_CALLBACK_URI),
            'urlAuthorize'            =>  $this->oauth_authority . $this::OAUTH_AUTHORIZE_ENDPOINT,
            'urlAccessToken'          =>  $this->oauth_authority . $this::OAUTH_TOKEN_ENDPOINT,
            'urlResourceOwnerDetails' => '',
            'scopes'                  => implode(' ', $this->oauth_scopes),
        ]);

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

    public function oauthCallback(): \League\OAuth2\Client\Token\AccessTokenInterface|false
    {
        $this->log('oauthCallback()');

        if (!isset($_SERVER['REQUEST_URI']) || !str_starts_with($_SERVER['REQUEST_URI'], $this::OAUTH_CALLBACK_URI)) {
            error_log('in oauthCallback(), request uri does not match');
            return null;
        }

        $expected_state_hashed = $_COOKIE[$this::OAUTH_SESSION_ID_COOKIE_NAME] ?? null;

        // Remove the cookies.
        $this->deleteCookie($this::OAUTH_SESSION_ID_COOKIE_NAME);
        error_log('Removed the session cookie');
        // $this->setCookie($this::OAUTH_ORIGIN_COOKIE_NAME, '', $this->now -1);

        if (!isset($_GET['state']) || !isset($_GET['code'])) {
            // If there is no state or code in the query params,
            error_log('No state or code in the query params');
            return null;
            // header('Location: ' . $host . '/auth.php?type=error&message=No%20OAuth%20session');
        }

        $provided_state = $_GET['state'];

        $provided_state_hashed = hash('sha256', $provided_state . $_ENV['AUTH_SALT']);

        error_log($expected_state_hashed);
        error_log($provided_state_hashed);

        if (empty($expected_state_hashed)) {
            // If there is no expected state in the session,
            // do nothing and redirect to the home page.
            // header('Location: ' . $host . '/?type=error&message=Expected%20state%20not%20available');
            error_log('Expected state not available');
            return false;
        }

        if (empty($provided_state_hashed) || $expected_state_hashed !== $provided_state_hashed) {
            error_log('State does not match');
            return false;

            // header('Location: ' . $host . '/auth.php?type=error&message=State%20does%20not%20match');
        }

        // Authorization code should be in the "code" query param
        $auth_code = $_GET['code'];

        // Initialize the OAuth client
        $oAuthClient = new GenericProvider([
            'clientId'                => $this->oauth_app_id,
            'clientSecret'            => $this->oauth_app_secret,
            'redirectUri'             => \home_url($this::OAUTH_CALLBACK_URI),
            'urlAuthorize'            =>  $this->oauth_authority . $this::OAUTH_AUTHORIZE_ENDPOINT,
            'urlAccessToken'          =>  $this->oauth_authority . $this::OAUTH_TOKEN_ENDPOINT,
            'urlResourceOwnerDetails' => '',
            'scopes'                  => implode(' ', $this->oauth_scopes),
        ]);

        $accessToken = null;
        try {
            // Make the token request
            $accessToken = $oAuthClient->getAccessToken('authorization_code', [
                'code' => $auth_code
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            error_log('Error: ' . $e->getMessage());
            return false;
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

        // TODO logout.

        // If we've hit the callback endpoint, then handle the callback.
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

        // The callback returned false, so we should deny access.
        if ($access_token === false) {
            http_response_code(401);
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

        // Here is a good place to handle Azure AD/Entra ID authentication.
        error_log('Do Entra ID/Azure AD authentication here.');
        $this->oauthLogin();
    }
}

$auth = new Auth(['debug' => true]);
$auth->handlePageRequest('reader');
