Feature: Test Commenting is switched off on some posts

Scenario: Test comments are closed Acceptance Criteria
  Given I visit "/blog/laa-my-dry-january/"
  # Check that comments do not exist on the page
  Then I should not see "There are no comments yet. Be the first to leave a comment."
  # Check the comment section is not just closed but doesn't exist
  Then I should not see "Comments are now closed"
