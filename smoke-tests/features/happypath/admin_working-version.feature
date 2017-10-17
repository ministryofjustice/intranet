@skip
Feature: Test revisions on tabs template

Scenario: Test revisions on tabs template Acceptance Criteria
  # I log in to the editor
  Given I log into the intranet as "agency_editor"
  # Create a new post
  When I open the new page editor
  And I fill in the field with the selector "#title" with "Revisions smoke test"
  And I fill in the field with the selector "#content" with "Pre draft text"
  And I click the "Publish" button
  # Check the content was updated
  And I click the "View page" link
  Then I should see "Pre draft text"
  # It exists, let's make a revision
  When I click the "Edit Page" link
  And I click the "Save Draft" button
  Then I should see "/revisions-smoke-test-2/"
  # Now let's update the content
  When I fill in the field with the selector "#content" with "Post draft text"
  And I click the "Save Draft" button
  # Check the new draft was created and that the original has not been affected
  And I visit the preview link
  Then I should see "Post draft text"
  When I visit "/revisions-smoke-test/"
  Then I should see "Pre draft text"
  And I should not see "Post draft text"
  # Publish the page and check the updates work
  When I visit the preview link
  And I click the "Edit Page" link
  And I click the "Publish" button
  Then I should see "/revisions-smoke-test/"
  When I visit "/revisions-smoke-test/"
  Then I should see "Post draft text"
  # Test complete, let's revert everything for the next test
  When I click the "Edit Page" link
  When I click the "Move to Trash" link
  Then I should see "1 page moved to the Trash."
