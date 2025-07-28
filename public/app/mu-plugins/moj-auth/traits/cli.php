<?php

/**
 * This command is used to generate a JWT token for the intranet-archive service.
 * 
 * To test this command locally you can run, first run `make bash` to enter the container.
 * To run this command on a deployed container, first run `kubectl -n $NSP exec -it $POD -c fpm -- ash` to enter the container.
 * 
 * Usage:
 *  run: wp gen-jwt intranet-archive
 *       wp gen-jwt synergy
 */

namespace MOJ\Intranet;

use WP_CLI;

trait AuthCli
{
    const GENERATED_JWT_DURATION    = 60 * 60 * 24 * 365 * 3; // 3 years

    const VALID_ROLES = [
        'intranet-archive',
        'synergy',
    ];

    /**
     * Init the WP CLI command.
     * 
     * @return void
     */
    public function initCli() : void
    {
        // If the WP_CLI constant is defined and true, then we're running in the WP CLI environment.
        if (defined('WP_CLI') && WP_CLI) {
            // Register the command.
            WP_CLI::add_command('gen-jwt', [$this, 'generateJwtCommand']);
        }
    }

    /**
     * Generate a JWT token for the intranet-archive or synergy service.
     * 
     * @param array $args The arguments passed to the command.
     * @return void
     */
    public function generateJwtCommand(array $args): void
    {
        $this->log('in generateJwtCommand');

        WP_CLI::log('GenerateJwt starting');

        if (empty($args[0])) {
            WP_CLI::log('GenerateJwt the command was missing the role argument.');
            return;
        }

        if (!in_array($args[0], self::VALID_ROLES, true)) {
            WP_CLI::log('GenerateJwt the role argument is not valid, it must be one of: ' . implode(', ', self::VALID_ROLES));
            return;
        }

        // Generate a JWT with the requested role and a long expiry.
        [$jwt, $jwt_string] = $this->setJwt((object) [
            'roles' => [$args[0]],
            'expiry'   => time() + self::GENERATED_JWT_DURATION
        ]);

        // Log the JWT object, so the user can see the contents.
        WP_CLI::log('JWT contents: ' . print_r($jwt, true));

        // Log the JWT string, this should be copied by the user and can be set as a cookie with name `jwt`.
        WP_CLI::log('JWT cookie: ' . $jwt_string);

        WP_CLI::log('GenerateJwt complete');
    }
}
