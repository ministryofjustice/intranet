Feature: Test commenting is disabled on post

Background:
  Given I log into the intranet as "agency_editor"
  Then I should see "Editing as: HQ"
  When I create a post titled "Test comments are disabled" with comments closed
  Then I click the "View post" link

Scenario: Test that comments are closed on post
  # Two checks to confirm comments are closed
  Given I should see "Comments are now closed"
  And I should not see "Comment on this page"

  # Test finished. Removes test post that was generated.
  When I click the "Edit Post" link
  Then I should see "Test comments are disabled"
  And I click the "Move to Trash" link
  And I should see "1 post moved to the Trash."
