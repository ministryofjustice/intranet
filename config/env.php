<?php

namespace MOJ\Justice;

/**
 * A convenience function to get environment variable.
 *
 * The vlucas/phpdotenv package is used to load environment variables from a .env file.
 * The defined variables are made available in the $_ENV and $_SERVER super-globals.
 * This function provides a convenient way to access these variables, and variables
 * that have been set directly to $_ENV.
 *
 * @param string $key
 * @return mixed
 */

function env(string $key) : mixed
{
    $value = isset($_ENV[$key]) ? $_ENV[$key] : null;
    if($value === 'true') {
        return true;
    }
    if($value === 'false') {
        return false;
    }
    return $value;
}
