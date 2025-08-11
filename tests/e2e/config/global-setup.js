/**
 * This file is an adapted version from the e2e tests in the WordPress/wordpress-develop repository.
 * 
 * https://github.com/WordPress/wordpress-develop/blob/trunk/tests/e2e/config/global-setup.js
 */

/**
 * External dependencies
 */
import { request } from "@playwright/test";

/**
 * WordPress dependencies
 */
import { RequestUtils } from "@wordpress/e2e-test-utils-playwright";

/**
 *
 * @param {import('@playwright/test').FullConfig} config
 * @returns {Promise<void>}
 */
async function globalSetup(config) {
  const { storageState, adminURL } = config.projects[0].use;
  const storageStatePath =
    typeof storageState === "string" ? storageState : undefined;

  const requestContext = await request.newContext({
    baseURL: adminURL,
  });

  const requestUtils = new RequestUtils(requestContext, {
    storageStatePath,
  });

  // Authenticate and save the storageState to disk.
  await requestUtils.setupRest();

  /**
   * There are 2 places where we can setup the state for the tests:
   * 
   * 1. In `/bin/e2e.sh` we can use the WP_CLI.
   * 2. In this file, we can use the RequestUtils to make REST API calls.
   * 
   * Judgement should be used to determine which is the best place to do various setup tasks.
   */

  // Reset the test environment before running the tests.
  await Promise.all( [
  	// requestUtils.activateTheme( 'clarity' ),
  	// requestUtils.deleteAllPosts(),
  	// requestUtils.deleteAllBlocks(),
  	// requestUtils.resetPreferences(),
  ] );

  await requestContext.dispose();
}

export default globalSetup;
