Feature: Test primary navigation bar

Scenario: Test primary navigation bar [HQ] Acceptance Criteria
  Given I visit "/"
  # Check the header nav exists
  Then I expect to see a ".header-menu" element
  # Check the links are as expected
  When I am looking at the ".header-menu" component, I should see "Home"
  When I am looking at the ".header-menu" component, I should see "News"
  When I am looking at the ".header-menu" component, I should see "Events"
  When I am looking at the ".header-menu" component, I should see "Guidance & forms"
  When I am looking at the ".header-menu" component, I should see "About us"
  When I am looking at the ".header-menu" component, I should see "Blog"
