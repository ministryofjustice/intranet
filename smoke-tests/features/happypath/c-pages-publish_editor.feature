Feature: Editor is able to publish a default page

  Scenario: An agency editor is able to publish a default page
    Given I log into the intranet as "agency_editor"

    When I open the new page editor
    And I fill in the field with the selector "#title" with "Smoke test page"

    # Publish the page
    And I click the "Publish" button
    Then I should see "Page published."

    # Let's go check the page
    When I click the "View page" link
    Then I should see the page title "Smoke test page"
    And The current URL path is "/smoke-test-page/"

    # Now get rid of the page
    When I click the "Edit Page" link
    Then I should see "Smoke test page"
    When I click the "Move to Trash" link
    Then I should see "1 page moved to the Trash."
