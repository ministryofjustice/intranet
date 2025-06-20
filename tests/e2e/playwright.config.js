/**
 * External dependencies
 */
import path from 'node:path';
import { defineConfig } from '@playwright/test';

/**
 * WordPress dependencies
 */
const baseConfig = require( '@wordpress/scripts/config/playwright.config' );

process.env.WP_ARTIFACTS_PATH ??= path.join( process.cwd(), 'artifacts' );
process.env.STORAGE_STATE_PATH ??= path.join(
	process.env.WP_ARTIFACTS_PATH,
	'storage-states/admin.json'
);

const config = defineConfig( {
	...baseConfig,
    timeout: 10_000,
	globalSetup: require.resolve( './config/global-setup.js' ),
    // Disable the web server, as we are testing against a running WordPress instance
	webServer: null,
    use: {
        ...baseConfig.use,
        adminURL: `${baseConfig.use.baseURL}/wp/`
    }
} );

export default config;
