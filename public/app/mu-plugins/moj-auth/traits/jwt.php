<?php

namespace MOJ\Intranet;

/**
 * Do not allow access outside WP, 401.php or verify.php
 * 
 * @used-by Auth
 * @used-by Standalone401
 * @used-by StandaloneVerify
 */

defined('ABSPATH') || defined('DOING_STANDALONE_401') || defined('DOING_STANDALONE_VERIFY') || exit;

/**
 * JWT functions for MOJ\Intranet\Auth.
 */

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

trait AuthJwt
{
    private $jwt_secret = '';

    const JWT_ALGORITHM   = 'HS256';
    const JWT_COOKIE_NAME = 'jwt';
    const JWT_DURATION    = 60 * 60; // 1 hour
    const JWT_REFRESH     = 60 * 2; // 2 minutes

    /**
     * Init
     */

    public function initJwt(): void
    {
        $this->log('initJwt()');

        $this->jwt_secret = $_ENV['JWT_SECRET'];

        // Clear JWT_SECRET from $_ENV global. It's not required elsewhere in the app.
        unset($_ENV['JWT_SECRET']);
    }

    /**
     * Get the JWT from the request.
     * 
     * @return bool|object Returns false if the JWT is not found or an object if it is found.
     */

    public function getJwt(): false | object
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
            if ($e->getMessage() !== 'Expired token') {
                \Sentry\captureException($e);
            }
            $this->log($e->getMessage());
            return false;
        }

        if(!$decoded) {
            return $decoded;
        }

        if ($decoded->sub) {
            $this->sub = $decoded->sub;
        }

        return $decoded;
    }

    /**
     * Set a JWT cookie.
     * 
     * @return object Returns the JWT payload.
     */

    public function setJwt(object $args = new \stdClass()): object
    {
        $this->log('setJwt()');

        $expiry = isset($args->expiry) ? $args->expiry : $this->now + $this::JWT_DURATION;

        $cookie_expiry = isset($args->cookie_expiry) ? $args->cookie_expiry : $expiry;

        if (!$this->sub) {
            $this->sub = bin2hex(random_bytes(16));
        }

        $payload = [
            // Registered claims - https://datatracker.ietf.org/doc/html/rfc7519#section-4.1
            'sub' => $this->sub,
            'exp' => $expiry,
            // Public claims - https://www.iana.org/assignments/jwt/jwt.xhtml
            'roles' =>  isset($args->roles) ? $args->roles : [],
        ];
        
        // Custom claims - conditionally add login_attempts from $args or class property.
        if(!empty($args->login_attempts)) {
            $payload['login_attempts'] = $args->login_attempts;
        }
        
        // Custom claims - conditionally add success_url from $args or class property.
        if(!empty($args->success_url)) {
            $payload['success_url'] = $args->success_url;
        }

        $jwt = JWT::encode($payload, $this->jwt_secret, $this::JWT_ALGORITHM);

        $this->setCookie($this::JWT_COOKIE_NAME, $jwt, $cookie_expiry);

        return (object) $payload;
    }
}
