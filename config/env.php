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
    // Get the value from the $_ENV super-global
    $value = $_ENV[$key] ?? null;

    // Convert 'true'/'false' strings to boolean values
    if (in_array($value, ['true', 'false'], true)) {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    return $value;
}
