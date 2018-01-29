Feature: Test Quick links editor admin

  @pending
  # NOTE: this test relies on the `agency_editor` belonging to HQ,
  # but not belonging to HMCTS. If this ever changes, this test will break.
  #
  Scenario: Test Quick links editor admin Acceptance Criteria
    Given I log into the intranet as "agency_editor"
    When I click the "Quick Links" link
    Then I should see "Quick Links Settings"
    # Sanity check: should not be an existing `test link`
    And The field with selector ".acf-row:nth-last-child(2) .acf-field-text input" should not contain the value "Test link"
    # Add a new quick link
    Then I click the "Add Link" link
    And I fill in the field with the selector ".acf-row:nth-last-child(2) .acf-field-text input" with "Test link"
    And I fill in the field with the selector ".acf-row:nth-last-child(2) .acf-field-url input" with "http://www.test-url.com"
    Then I click the "Update" button
    # Give the DB a chance to update
    Then I pause for "1" seconds
    # Test that the new link is there
    When I visit "/?agency=hq"
    Then I should see a link which says "Test link" and goes to "http://www.test-url.com"
    # Test that it's only on HQ
    Then I visit "/?agency=hmcts"
    Then I should not see "Test link"
    # All good. Now let's delete it.
    Then I visit "%{WP_ADMIN}/admin.php?page=quick-links-settings"
    #Then I hover over the ".acf-row:nth-last-child(2)" element
    Then I click the hidden ".acf-row:nth-last-child(2) .acf-icon.-minus" element
    Then I pause for "1" seconds
    # Sanity check: should not be an existing `test link`
    And The field with selector ".acf-row:nth-last-child(2) .acf-field-text input" should not contain the value "Test link"
    Then I click the "Update" button
    # Give the DB a chance to update
    Then I pause for "1" seconds
    # Make sure it's gone
    When I visit "/?agency=hq"
    Then I should not see "Test link"

