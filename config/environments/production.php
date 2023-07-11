<?php
/** Production */
ini_set('display_errors', 0);

const WP_DEBUG_DISPLAY = false;
const SCRIPT_DEBUG = false;
/** Disable all file modifications including updates and update notifications */
const DISALLOW_FILE_MODS = true;

const SENTRY_TRACES_SAMPLE_RATE = 0.2;
const SENTRY_PROFILE_SAMPLE_RATE = 0.2;
