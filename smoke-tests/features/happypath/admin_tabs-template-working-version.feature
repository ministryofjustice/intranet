Feature: Test revisions on tabs template

Scenario: Test revisions on tabs template Acceptance Criteria
  # Navigate to the admin
  Given I log into the intranet as "agency_editor"
  # Visit a tabs template page
  When I visit "%{WP_ADMIN}/post.php?post=82082&action=edit"
  And I click the "Save Draft" button
  Then I should see "helicopter-leadership-2/"
  And I should see "Status: Draft"
  And The field with selector "#title" should contain the value "Helicopter Leadership"
  When I click the "#post-preview" element
  And I switch to the last window opened
  Then The content should contain "Helicopter Leadership.+In the Navy.+Anyone can use this approach.+If you want to learn more"
  # Now get rid of the draft
  When I click the "Edit Post" link
  When I click the "Move to Trash" link
  Then I should see "1 post moved to the Trash."
