Feature: Commenting on posts

Scenario: Test an editor can turn commenting on and off

  Given I log into the intranet as "agency_editor"

  # Turn comments off on the post
  # (Assumes commenting is turned on by default when a post is created. This is the current global site default as of 08.08.17)
  When I create a new post
  And I fill in the field with the selector "#title" with "Test an editor can turn commenting on and off"
  When I click the radio button "Show the comments section"
  Then I should not see "Allow people to add new comments"

  # Publish the post
  When I click the "Publish" button
  Then I should see "Post published."

  # Check to see that there are no comments showing on the post
  When I click the "View post" link
  Then I should see "Test an editor can turn commenting on and off"
  Then I should not see "There are no comments yet. Be the first to leave a comment."
  Then I should not see "Comments are now closed"
  And I should not see "Comments"

  # Turn comments on in the post
  When I click the "Edit Post" link
  When I click the radio button "Show the comments section"
  # We don't check the second radio button that follows, titled "Allow people to add new comments" because by default it becomes checked when you check the first "Show the comments section" and by checking it again, would result in it being turned off.

  # Publish the post
  When I click the "Update" button
  Then I should see "Post updated."

  # Check that there are comments showing on the post
  When I click the "View post" link
  Then I should see "Test an editor can turn commenting on and off"
  Then I should see "Comment on this page"
  And I should see "Comments"

  # Remove the post
  When I click the "Edit Post" link
  And I click the "Move to Trash" link
  And I should see "1 post moved to the Trash."
