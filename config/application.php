<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;
use function Env\env;

/**
 * @const MOJ_ROOT_DIR string
 *
 * Resolves the system path directory containing the
 * application's files
 *
 * @example /var/www/html
 */
define('MOJ_ROOT_DIR', dirname(__DIR__));

/**
 * Website directory, system path
 */
$webroot_dir = MOJ_ROOT_DIR . '/public';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 * .env.local will override .env if it exists
 */
if (file_exists(MOJ_ROOT_DIR . '/.env')) {
    $env_files = file_exists(MOJ_ROOT_DIR . '/.env.local')
        ? ['.env', '.env.local']
        : ['.env'];

    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(MOJ_ROOT_DIR, $env_files, false);
    $dotenv->load();
    $dotenv->required(['WP_HOME', 'WP_SITEURL']);
    if (!env('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    }
}

/**
 * @const WP_ENV string
 *
 * Contains our global environment string.
 * Can be one of:
 *  - development
 *  - staging
 *  - production
 * @example production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');

/**
 * @const WP_DEFAULT_THEME string
 *
 * Define the default theme to run in WordPress.
 */
Config::define('WP_DEFAULT_THEME', 'clarity');

/**
 * @const EP_HOST string
 *
 * Define the URL ElasticPress will use to manage
 * search functionality
 * @example http://host.opensearch.service.com:8890
 */
Config::define('EP_HOST', env('OPENSEARCH_URL'));

/**
 * Infer WP_ENVIRONMENT_TYPE based on WP_ENV
 */
if (!env('WP_ENVIRONMENT_TYPE') && in_array(WP_ENV, ['production', 'staging', 'development', 'local'])) {
    Config::define('WP_ENVIRONMENT_TYPE', WP_ENV);
}

/**
 * URLs
 */
Config::define('WP_HOME', env('WP_HOME'));
Config::define('WP_SITEURL', env('WP_SITEURL'));
Config::define('LOOPBACK_URL', env('LOOPBACK_URL') ?? 'http://127.0.0.1:8080');
// Explicitly set cookie paths, to prevent conflicting wordpress_logged_in... wordpress_sec_... cookies.
Config::define('COOKIEPATH', '/');
Config::define('SITECOOKIEPATH', '/');
Config::define('ADMIN_COOKIE_PATH', '/');

/**
 * Custom Content Directory
 */
Config::define('CONTENT_DIR', '/app');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

/**
 * DB settings
 */
if (env('DB_SSL')) {
    Config::define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}

Config::define('DB_NAME', env('DB_NAME'));
Config::define('DB_USER', env('DB_USER'));
Config::define('DB_PASSWORD', env('DB_PASSWORD'));
Config::define('DB_HOST', env('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

if (env('DATABASE_URL')) {
    $dsn = (object)parse_url(env('DATABASE_URL'));

    Config::define('DB_NAME', substr($dsn->path, 1));
    Config::define('DB_USER', $dsn->user);
    Config::define('DB_PASSWORD', $dsn->pass ?? null);
    Config::define('DB_HOST', isset($dsn->port) ? "$dsn->host:$dsn->port" : $dsn->host);
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Custom Settings
 */
Config::define('AUTOMATIC_UPDATER_DISABLED', true);

// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT', true);

// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

// Limit the number of post revisions
Config::define('WP_POST_REVISIONS', env('WP_POST_REVISIONS') ?? true);

// API key for notifications.service.gov.uk email service
Config::define('GOV_NOTIFY_API_KEY', env('GOV_NOTIFY_API_KEY') ?? null);

// Turn off WP Cron - use `wp cron` instead
Config::define('DISABLE_WP_CRON', true);

// Disable php script concatenation at runtime - we serve WP assets via nginx
Config::define('CONCATENATE_SCRIPTS', false);

// For completeness, disable CSS and script compression at runtime
// These should be irrelevant because CONCATENATE_SCRIPTS is false
Config::define('COMPRESS_CSS', false);
Config::define('COMPRESS_SCRIPTS', false);

// Enable the authentication mu-plugin.
Config::define('MOJ_AUTH_ENABLED', true);

// ACF License Key
Config::define('ACF_PRO_LICENSE', env('ACF_PRO_LICENSE'));

// Set to true to turn off automatic optimization of your images.
// i.e. during migration, when using WP Offload Media - Metadata Tool.
Config::define('EWWW_IMAGE_OPTIMIZER_NOAUTO', env('EWWW_IMAGE_OPTIMIZER_NOAUTO'));

// Enable "agency" mode, which hides all external links and support resources.
Config::define('EWWWIO_WHITELABEL', true);

// Disable rewrite of enqueued assets to CDN.
Config::define('DISABLE_CDN_ASSETS', env('DISABLE_CDN_ASSETS'));

// Set the Intranet Archive URL and agencies - for the link on the dashboard.
Config::define('INTRANET_ARCHIVE_URL', env('INTRANET_ARCHIVE_URL'));
// Set the shared secret for the Intranet Archive.
Config::define('INTRANET_ARCHIVE_SHARED_SECRET', env('INTRANET_ARCHIVE_SHARED_SECRET'));

/**
 * Debugging Settings
 */
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', true);
Config::define('WP_DEBUG_LOG', '/dev/stderr');
Config::define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');
// Additional logging for the authentication mu-plugin.
Config::define('MOJ_AUTH_DEBUG', env('MOJ_AUTH_DEBUG'));
// Version of the authentication mu-plugin.
Config::define('MOJ_AUTH_VERSION', env('MOJ_AUTH_VERSION'));

/**
 * WP Redis config.
 * 
 * In object-cache.php, specific variables are read via $_SERVER
 * CACHE_HOST, CACHE_PORT, CACHE_PASSWORD, CACHE_DB, CACHE_TIMEOUT
 * They can be set via ENV VARS or here.
 * 
 * Other config entries use constants and can be defined as usual.
 * 
 * @see https://github.com/pantheon-systems/wp-redis
 */

if (!isset($_SERVER['CACHE_TIMEOUT'])) {
    // Set a timeout over 1s to allow for tls.
    $_SERVER['CACHE_TIMEOUT'] = 2500;
}


// Disable the caching if CACHE_HOST is empty, or via WP_REDIS_DISABLED - in case of emergency.
Config::define('WP_REDIS_DISABLED', empty($_SERVER['CACHE_HOST']) || env('WP_REDIS_DISABLED'));
// Use Relay redis client, over predis.
Config::define('WP_REDIS_USE_RELAY', env('WP_REDIS_USE_RELAY'));
// Set default expiry to 1hour.
Config::define('WP_REDIS_DEFAULT_EXPIRE_SECONDS', 3600);
// This salt prefixes the cache keys.
Config::define('WP_CACHE_KEY_SALT', env('WP_CACHE_KEY_SALT') ?: WP_ENV);


/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

/**
 * WP Offload Media settings
 *
 * If we don't set AS3CF_SETTINGS here, we can use the
 * plugin GUI to configure the settings during debugging.
 */
if (file_exists(__DIR__ . '/wp-offload-media.php')) {
    require_once __DIR__ . '/wp-offload-media.php';
}

/**
 * Environment-specific settings
 */
$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
    require_once $env_config;
}


Config::apply();

// settings are dependent on a plugin
if (file_exists(MOJ_ROOT_DIR . '/public/app/plugins/wp-sentry/wp-sentry.php')) {
    require_once __DIR__ . '/wp-sentry.php';
}

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}
