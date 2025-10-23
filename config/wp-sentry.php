<?php

use function MOJ\Justice\env;

/**
 * Initialise Sentry options
 */
define('WP_SENTRY_PHP_DSN', env('SENTRY_DSN'));
define('WP_SENTRY_BROWSER_DSN', env('SENTRY_DSN'));
define( 'WP_SENTRY_ENV', WP_ENV . (env('SENTRY_DEV_ID') ?? '') );

const WP_SENTRY_SEND_DEFAULT_PII = true;
const WP_SENTRY_ERROR_TYPES = E_ALL & ~E_NOTICE & ~E_USER_NOTICE;
const WP_SENTRY_BROWSER_LOGIN_ENABLED = false;
const WP_SENTRY_BROWSER_TRACES_SAMPLE_RATE = 0.3;
const WP_SENTRY_BROWSER_REPLAYS_SESSION_SAMPLE_RATE = 0.1; // replaysSessionSampleRate
const WP_SENTRY_BROWSER_REPLAYS_ON_ERROR_SAMPLE_RATE = 1.0; // replaysOnErrorSampleRate


//require_once MOJ_ROOT_DIR. '/public/app/plugins/wp-sentry/wp-sentry.php';

/*if (env('SENTRY_DSN')) {
    try {
        \Sentry\init([
            'dsn' => env('SENTRY_DSN'),
            'environment' => WP_ENV. env('SENTRY_DEV_ID') ?? '',
            'traces_sample_rate' => Config::get('SENTRY_TRACES_SAMPLE_RATE'),
            'profiles_sample_rate' => Config::get('SENTRY_PROFILE_SAMPLE_RATE')
        ]);
    } catch (Exception $error) {
        echo "<pre>" . print_r($error, true) . "</pre>";
    }
}*/

