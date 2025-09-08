import { join } from 'node:path';

/**
 * Customised version of the `visitAdminPage` function to visit an admin page in a WordPress site.
 *
 * This version of the function is copied from the Gutenberg e2e test utils and is used to navigate to a specific admin page.
 * The original version doesn't work with admin URLs prefixed with `/wp`.
 *
 * @see https://github.com/WordPress/gutenberg/blob/5d2f86a1e0db8708d875b1ca04f9e25541a822b0/packages/e2e-test-utils-playwright/src/admin/visit-admin-page.ts
 *
 * @param {Object} admin - The admin object containing the page and pageUtils.
 * @param {string} adminPath - The path to the admin page to visit.
 * @param {string} [query] - Optional query string to append to the URL.
 * @param {string} [adminPrefix='wp'] - The prefix for the admin URL, default is 'wp'.
 * @returns {Promise<unknown>} Resolves when the page is successfully loaded.
 * @throws {Error} Throws an error if the page is not loaded correctly or if there is an unexpected error in the page content.
 */

export async function visitAdminPage(
    admin,
    adminPath,
    query,
    adminPrefix = 'wp'
) {
    await admin.page.goto(
        join( adminPrefix, 'wp-admin', adminPath ) + ( query ? `?${ query }` : '' )
    );

    // Handle upgrade required screen
    if ( admin.pageUtils.isCurrentURL( `${adminPrefix}/wp/wp-admin/upgrade.php` ) ) {
        // Click update
        await admin.page.click( '.button.button-large.button-primary' );
        // Click continue
        await admin.page.click( '.button.button-large' );
    }

    if ( admin.pageUtils.isCurrentURL( `${adminPrefix}/wp-login.php` ) ) {
        throw new Error( 'Not logged in' );
    }

    const error = await admin.getPageError();
    if ( error ) {
        throw new Error( 'Unexpected error in page content: ' + error );
    }
}
