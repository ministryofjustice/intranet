<?php
/** Development */

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

const SAVEQUERIES = true;
const WP_DEBUG = true;
const SCRIPT_DEBUG = true;
define('WP_DEBUG_LOG', env('WP_DEBUG_LOG'));

const SENTRY_TRACES_SAMPLE_RATE = 1.0;
const SENTRY_PROFILE_SAMPLE_RATE = 1.0;
