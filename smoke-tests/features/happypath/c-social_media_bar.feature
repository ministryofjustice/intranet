Feature: Social media bar on posts

Background: Create a post to test media bar
  Given I log into the intranet as "agency_editor"
  When I create a new post
  Then I fill in the field with the selector "#title" with "Social media bar test"
  When I click the "Publish" button
  Then I should see "Post published."
  And I click the "View post" link

Scenario: Test that the social media bar is displaying on the post
  Given I should see "Social media bar test"
  And I should see "Share by email"
  Then I expect to see a ".like-link" element

  # Remove the test post
  When I click the "Edit Post" link
  Then I should see "Social media bar test"
  And I click the "Move to Trash" link
  And I should see "1 post moved to the Trash."
