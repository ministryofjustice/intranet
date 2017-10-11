Feature: Test embedded Youtube videos show in the induction page
  # https://www.pivotaltracker.com/story/show/148765501

  Scenario: Test embedded Youtube videos show
    # Navigate to the induction page
    When I visit "/guidance/hr/induction/"

    # Check the tab `Staff`
    Then I should see "Welcome to MoJ video and New starter’s experiences video"
    And I should see an embedded Youtube video with ID "KQmVj6UTj5U"
    And I should see an embedded Youtube video with ID "yZjFn1yRlZ8"

    # Check the tab `Managers`
    When I click the "Managers" link
    Then I should see "Induction guide for managers"
    And I should see an embedded Youtube video with ID "KQmVj6UTj5U"
    And I should see an embedded Youtube video with ID "yZjFn1yRlZ8"
