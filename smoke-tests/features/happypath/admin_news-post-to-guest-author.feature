Feature: Test change news post to guest author

  # Once we are able to properly create users with the right permissions,
  # we might consider creating a 'Test Guest Author' so this test is a bit
  # more robust not relying on any other existing users.
  #
  Scenario: Test change news post to guest author Acceptance Criteria
    Given I log into the intranet as "agency_editor"

    # Setup a test post
    And I create a new post
    And I fill in the field with the selector "#title" with "Change authors smoke test"
    And I fill in the field with the selector "#content" with "This is a test"
    When I click the "Publish" button
    And I click the "View post" link
    Then I should see "Change authors smoke test"
    And I should see "Agency Editor Test" as the post author

    When I click the "Edit Post" link
    And I pause for "2" seconds
    # I search for the guest author
    And I fill the autocomplete "coauthorsinput[]" with "Adrian Draper"
    # We look for the slug as it will appear in the autocomplete results
    Then I should see "adrian-draper"
    # I add the author to the 'authors' list
    When I click the ".ac_match" element
    # Then I delete the original author
    And I click the hidden ".coauthor-row:first-of-type .delete-coauthor" element and accept the confirmation
    When I click the "Update" button
    And I pause for "1" seconds

    # Let's go check to see if it worked
    And I click the "View Post" link
    Then I should see "Adrian Draper" as the post author
    And I should not see "Agency Editor Test" as the post author

    # Reset the post to its original state
    When I click the "Edit Post" link
    And I pause for "2" seconds
    Then I should see "Adrian Draper"
    When I fill the autocomplete "coauthorsinput[]" with "Agency Editor Test"
    Then I should see "agency_editor"
    And I click the ".ac_match" element
    And I click the hidden ".coauthor-row:first-of-type .delete-coauthor" element and accept the confirmation
    Then I click the "Update" button
    And I pause for "1" seconds
    When I click the "View Post" link
    Then I should see "Agency Editor Test" as the post author
    And I should not see "Adrian Draper" as the post author

    # Test complete, let's revert everything for the next test
    When I click the "Edit Post" link
    When I click the "Move to Trash" link
    Then I should see "1 post moved to the Trash."
