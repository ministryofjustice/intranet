<?php
/** Production */
ini_set('display_errors', 0);
const WP_DEBUG_DISPLAY = false;
const SCRIPT_DEBUG = false;
/** Disable all file modifications including updates and update notifications */
const DISALLOW_FILE_MODS = true;

/** Elasticsearch  /  ElasticPress */
const EP_HOST_URL = "https://search-intranet-prod-4ckbj3hdyvuznpnsaustbud6mu.eu-west-1.es.amazonaws.com/";
