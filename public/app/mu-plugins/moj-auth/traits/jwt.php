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

    // JWT roles and their conditions.
    const JWT_ROLES = [
        // The reader role has no conditions.
        'reader' => true,
        // The intranet-archive role has a condition that the IP group must be 5.
        'intranet-archive' => [
            'conditions' => [
                'ipGroupIn' => [5]
            ]
        ]
    ];

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
            $this->log($e->getMessage(), null, 'error');
            return false;
        }

        if (!$decoded) {
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
     * @return [object, string] Returns the JWT payload.
     */
    public function setJwt(object $args = new \stdClass()): array
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

        // Custom claims - conditionally add failed_callbacks from $args or class property.
        if (isset($args->failed_callbacks)) {
            $payload['failed_callbacks'] = $args->failed_callbacks;
        }

        // Custom claims - conditionally add success_url from $args or class property.
        if (!empty($args->success_url)) {
            $payload['success_url'] = $args->success_url;
        }

        $jwt_string = JWT::encode($payload, $this->jwt_secret, $this::JWT_ALGORITHM);

        $this->setCookie($this::JWT_COOKIE_NAME, $jwt_string, $cookie_expiry);

        return [(object) $payload, $jwt_string];
    }


    /**
     * Verify the JWT roles.
     * 
     * @param [string] $jwt_roles The roles from the JWT.
     * @return bool
     */
    public function verifyJwtRoles(array $jwt_roles): bool
    {

        $this->log('verifyJwtRoles()');

        if (!is_array($jwt_roles)) {
            $this->log('verifyJwtRoles() $jwt_roles is not an array.', null, 'error');
            return false;
        }

        return $this->arrayAny($jwt_roles, function ($role) {
            $role_props = $this::JWT_ROLES[$role] ?? false;

            if (!$role_props) {
                $this->log('verifyJwtRoles() jwt role is not defined in JWT_ROLES.', null, 'error');
                return false;
            }

            // If the role has no conditions, then return true.
            if ($role_props === true) {
                return true;
            }

            $conditions = $role_props['conditions'] ?? false;

            if (!$conditions) {
                $this->log('verifyJwtRoles() $conditions is false.', null, 'error');
                return false;
            }

            return $this->arrayAll($conditions, function ($condition, $key) {
                $method = 'verifyJwtRole' . ucfirst($key);

                if (!method_exists($this, $method)) {
                    $this->log('verifyJwtRoles() $method does not exist.', null, 'error');
                    return false;
                }

                return $this->$method($condition);
            });
        });
    }

    /**
     * Verify the IPs group of the request is in array.
     * 
     * @param [int] $valid_groups
     * @return bool
     */
    public function verifyJwtRoleIpGroupIn(array $valid_groups): bool
    {
        $request_ip_group = $_SERVER['HTTP_X_MOJ_IP_GROUP'] ?? '';

        if (!in_array((int) $request_ip_group, $valid_groups)) {
            $this->log('verifyJwtRoleIpGroupIn() $request_ip_group is not in $valid_groups.', null, 'error');
            return false;
        }

        return true;
    }
}
