Feature: Test phase bar

Scenario: Test phase bar [HQ] Acceptance Criteria
  Given I visit "/"
  # Check the phase bar nav exists
  Then I expect to see a ".beta-banner" element
  # Check the text is as expected
  When I am looking at the ".beta-banner" component, I should see "Tell us what you think and help us improve (link opens in a new browser window) the intranet. To report a problem, please use the link in the footer."
  Then I should see a link which says "help us improve" and goes to "https://www.surveymonkey.co.uk/r/8VDHMY8"
