<?php

namespace Microsoft\Kiota\Authentication\Cache;

use League\OAuth2\Client\Token\AccessToken;
use Roots\WPConfig\Config;

/**
 * Class TransientAccessTokenCache
 *
 * WordPress Transient cache for access tokens
 */
class TransientAccessTokenCache implements AccessTokenCache
{

    const PREFIX = 'moj_auth_';

    private $debug;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * Return cached access token if available, else return null
     *
     * @param string $identity
     * @return AccessToken|null
     */
    public function getAccessToken(string $identity): ?AccessToken
    {
        if($this->debug) {
            error_log('MOJ_AUTH: getAccessToken(), getting transient: ' . $identity);
        }
        return get_transient($this::PREFIX .  $identity);
    }

    /**
     * Persist access token in cache
     *
     * @param string $identity
     * @param AccessToken $accessToken
     * @return void
     */
    public function persistAccessToken(string $identity, AccessToken $accessToken): void
    {
        if($this->debug) {
            error_log('MOJ_AUTH: persistAccessToken(), setting transient: ' . $identity . ' for ' . $accessToken->getExpires() - time() . ' seconds');
        }
        set_transient($this::PREFIX . $identity, $accessToken, $accessToken->getExpires() - time());
    }
}
