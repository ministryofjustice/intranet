import { test, expect } from "@wordpress/e2e-test-utils-playwright";

test.describe("Agency Switcher", () => {
  /* @type {import('@playwright/test').BrowserContext} */
  let newUserContext;

  /* @type {import('@playwright/test').Page} */
  let newUserPage;

  /* @type string */
  let baseURL;

  /**
   * Test lifecycle hooks to set up the test environment.
   */
  test.beforeAll(async ({ browser }, testInfo) => {
    // Get the base URL from the test info.
    baseURL = testInfo.project.use.baseURL;

    // Create a new browser context without any cookies.
    newUserContext = await browser.newContext({
      baseURL,
      storageState: {},
    });

    newUserPage = await newUserContext.newPage();
  });

  test.beforeEach(async () => {
    // Clear all cookies before each test to ensure a clean state.
    await newUserPage.context().clearCookies();
  });

  test.afterAll(async () => {
    // Close the logged out page and context after all tests are done.
    await newUserPage.close();
    await newUserContext.close();
  });

  /**
   * Test cases for the Agency Switcher functionality.
   */
  test("Should load when accessed directly", async () => {
    await newUserPage.goto("agency-switcher");

    await expect(
      newUserPage.getByRole("heading", {
        name: "Welcome to the Ministry of Justice intranet",
        level: 1,
      }),
    ).toBeVisible();
  });

  test("Should have a link in the header", async () => {
    // Set the agency cookie so we won't be redirected to the agency switcher.
    await newUserPage.context().addCookies([
      {
        name: "dw_agency",
        value: "hmcts",
        url: baseURL,
      },
    ]);
    await newUserPage.goto("/");

    const switchLink = newUserPage.getByRole("link", {
      name: "Switch to other intranet",
    });
    await expect(switchLink).toBeVisible();
    await expect(switchLink).toHaveAttribute("href", "/agency-switcher");
  });

  test("Should load the selected agency's homepage", async () => {
    // Set a UAT cookie, so the modal is not shown.
    await newUserPage.context().addCookies([
      {
        name: "moj_uat_session",
        value: "user_accepted",
        url: baseURL,
      },
    ]);

    // Navigate to the agency switcher page.
    await newUserPage.goto("agency-switcher");

    // Click on the link for the HMCTS agency.
    await newUserPage
      .getByRole("link", { name: "HM Courts & Tribunals Service" })
      .click();

    // Check that the URL is `/`
    expect(newUserPage.url()).toBe(`${baseURL}/?agency=hmcts`);

    // Check the cookie is set correctly.
    const cookies = await newUserPage.context().cookies();
    const agencyCookie = cookies.find(
      (cookie) => cookie.name === "dw_agency",
    );
    expect(agencyCookie).toBeDefined();
    expect(agencyCookie.value).toBe("hmcts");

    // Check that header>.agency-title is set to "HM Courts & Tribunals Service".
    const agencyTitle = await newUserPage
      .locator("header span.agency-title")
      .textContent();
    expect(agencyTitle).toBe("HM Courts & Tribunals Service");
  });

  test("Should be redirected to, when no agency cookie is set", async () => {
    // Construct the expected URL for the agency switcher.
    const expectedURL = `${baseURL}/agency-switcher/?send_back=${encodeURIComponent(
      `${baseURL}/`,
    )}`;

    await newUserPage.goto("/");

    expect(newUserPage.url()).toBe(expectedURL);
  });

  test("Should not be redirected to, when an agency cookie is set", async ({}) => {
    // Set the agency cookie in the new user context.
    await newUserContext.addCookies([
      {
        name: "dw_agency",
        value: "hmcts",
        url: baseURL,
      },
    ]);

    // Navigate to the home page.
    await newUserPage.goto("/");

    // Check that the URL is the home page and not the agency switcher.
    expect(newUserPage.url()).toBe(`${baseURL}/`);
  });
});
