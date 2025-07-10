import { test, expect } from "@wordpress/e2e-test-utils-playwright";

import {visitAdminPage} from "../utils";

test.describe("Homepage Settings", () => {
    test("Admin menu should have homepage settings link", async ({ admin, page }) => {
        await admin.visitAdminPage("/");
        await expect(
            page.locator('#adminmenu').getByRole("link", { href: "admin.php?page=homepage-settings", name: 'Homepage'})
        ).toBeVisible();
    });

    test("Homepage settings should be accessible", async ({ admin, page }) => {
        await visitAdminPage(admin, "admin.php", 'page=homepage-settings');

        await expect(
            page.getByRole("heading", { name: "Homepage", level: 1 })
        ).toBeVisible();
    });

    /**
     * This test suite is incomplete.
     * 
     * The following test is a placeholder and should test the other functionality of the homepage settings.
     * If necessary, it should be split into multiple tests to cover all functionality.
     */
    test.fixme("Homepage settings should be reflected on homepage", async ({ admin, page }) => {
    });
});
