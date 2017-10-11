Feature: Test Logo bar

Scenario: Test Logo bar [HQ] Acceptance Criteria
  Given I visit "/"
  # Check the intranet switcher link exists
  Then I should see "Switch to other intranet"
  Then I should see an image called "moj_logo.png"



