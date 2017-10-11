Feature: Media grid template Youtube videos

  Scenario: Test an editor can embed Youtube videos with media grid
    Given I log into the intranet as "agency_editor"

    # Create a page with media grid template
    When I open the new page editor
    And I fill in the field with the selector "#title" with "My smoke test page test"
    And I fill in the field with the selector "#content" with "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    And I pause for "1" seconds
    And I change the current template to "Media grid"
    Then I should see "Media grid options"
    When I add a new media grid row
    Then I should see "Feature video"
    And I enter the value "82OUNZQesng" in the media grid section "feature_video"

    # Publish the page
    When I click the "Publish" button
    Then I should see "Page published."

    # Let's go check to see if the video snippet is there
    When I click the "View page" link
    Then I should see "My smoke test page test"
    And I should see "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    And I should see an embedded Youtube video with ID "82OUNZQesng"

    # Now get rid of the page
    When I click the "Edit Page" link
    Then I should see "My smoke test page test"
    And I click the "Move to Trash" link
    And I should see "1 page moved to the Trash."
