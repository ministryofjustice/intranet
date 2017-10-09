Feature: Test Agency switcher component

Scenario: Test agency switcher Acceptance Criteria
  Given I visit "/"
  # Make sure I am on HQ
  Then I should see "Ministry of Justice HQ"
  # Switch to HMTCS intranet
  When I click the "Switch to other intranet" link
  Then I click the "HM Courts & Tribunals Service" link
  # Make sure I am on HMCTS
  Then I should see "HM Courts & Tribunals Service"

