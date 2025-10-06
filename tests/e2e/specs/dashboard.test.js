// This file is taken from the WordPress e2e tests.
// https://github.com/WordPress/wordpress-develop/blob/6.4/tests/e2e/specs/hello.test.js

/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Dashboard', () => {
	test( 'Should load properly', async ( { admin, page }) => {
		await admin.visitAdminPage( '/' );
		await expect(
			page.getByRole('heading', { name: 'Dashboard', level: 1 })
		).toBeVisible();
	} );
} );
