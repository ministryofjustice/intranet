<?php

/** @var string Directory containing all of the site's files */
define('MOJ_ROOT_DIR', dirname(__DIR__));

/** @var string Document Root */
$webroot_dir = MOJ_ROOT_DIR . '/web';

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new Dotenv\Dotenv(MOJ_ROOT_DIR);
if (file_exists(MOJ_ROOT_DIR . '/.env')) {
    $dotenv->load();
    $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL', 'WP_ENV']);
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');

/**
 * Initialise Sentry
 */
Sentry\init([
    'dsn' => 'https://85635e3372244483b86823c4f75dcee2@o345774.ingest.sentry.io/6143405',
    'environment'=> WP_ENV . (env('SENTRY_DEV_ID') ?? ''),
]);

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
    require_once $env_config;
}

/** Elasticsearch  /  ElasticPress */
define("EP_HOST", env('ELASTICSEARCH_HOST'));

/**
 * URLs
 */
define('WP_HOME', env('WP_HOME'));
define('WP_SITEURL', env('WP_SITEURL'));

/**
 * Custom Content Directory
 */
const CONTENT_DIR = '/app';
define('WP_CONTENT_DIR', $webroot_dir . CONTENT_DIR);
const WP_CONTENT_URL = WP_HOME . CONTENT_DIR;

/**
 * DB settings
 */
define('DB_NAME', env('DB_NAME'));
define('DB_USER', env('DB_USER'));
define('DB_PASSWORD', env('DB_PASSWORD'));
define('DB_HOST', env('DB_HOST') ?: 'localhost');
const DB_CHARSET = 'utf8mb4';
const DB_COLLATE = '';
$table_prefix = env('DB_PREFIX') ?: 'wp_';

/**
 * Authentication Unique Keys and Salts
 */
define('AUTH_KEY', env('AUTH_KEY'));
define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
define('NONCE_KEY', env('NONCE_KEY'));
define('AUTH_SALT', env('AUTH_SALT'));
define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Custom Settings
 */
const AUTOMATIC_UPDATER_DISABLED = true;
const DISABLE_WP_CRON = true;
const DISALLOW_FILE_EDIT = true;
define('S3_UPLOADS_BASE_URL', env('S3_UPLOADS_BASE_URL') ?? false);

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}
