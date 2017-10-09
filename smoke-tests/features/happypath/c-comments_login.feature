Feature: Login via email for commenting

Background:
  Given I log into the intranet as "agency_editor"
  Then I should see "Editing as: HQ"
  Then I create and populate a blog post titled "Testing commenting"
  # I am currently logged in but now that I have created the post, the scenario requires to be logged out so
  And I logout

Scenario: Login so I can comment
  Given I visit "/blog/testing-commenting/"
  And I am not logged in to comment
  Then I should see "Comment on this page"
  When I log in using email
  Then I should be logged in to comment
  And I can post a comment
  # I need to logout now to login as an agency editor to remove test post
  And I logout

  # Remove the test post
  Then I log into the intranet as "agency_editor"
  When I visit "/blog/testing-commenting/"
  When I click the "Edit Post" link
  Then I click the "Move to Trash" link
  And I should see "1 post moved to the Trash."
