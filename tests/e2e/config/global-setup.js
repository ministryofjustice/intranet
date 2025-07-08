/**
 * External dependencies
 */
import { request } from "@playwright/test";

/**
 * WordPress dependencies
 */
import { RequestUtils } from "@wordpress/e2e-test-utils-playwright";

// const WP_ADMIN_USER = {
//   username: process.env.WP_USERNAME || "admin",
//   password: process.env.WP_PASSWORD || "password",
// };

/**
 *
 * @param {import('@playwright/test').FullConfig} config
 * @returns {Promise<void>}
 */
async function globalSetup(config) {

  console.log("Running global setup for E2E tests...");

  const { storageState, adminURL } = config.projects[0].use;
  const storageStatePath =
    typeof storageState === "string" ? storageState : undefined;

  console.log('adminURL: ', adminURL);

  const requestContext = await request.newContext({
    baseURL: adminURL,
  });

  const requestUtils = new RequestUtils(requestContext, {
    storageStatePath,
    // user: WP_ADMIN_USER,
  });

  // Authenticate and save the storageState to disk.
  await requestUtils.setupRest();

  // Reset the test environment before running the tests.
  await Promise.all( [
  	requestUtils.activateTheme( 'clarity' ),
  	requestUtils.activatePlugin( 'clarity' ),
  	requestUtils.deleteAllPosts(),
  	requestUtils.deleteAllBlocks(),
  	requestUtils.resetPreferences(),
  ] );

  await requestContext.dispose();
}

export default globalSetup;
