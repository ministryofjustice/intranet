Feature: Test HQ homepage 'Events' component

Scenario: Test HQ homepage 'Events' component Acceptance Criteria
  # Navigate to the homepage for HQ
  Given I visit "/?agency=hq"
  And I pause for "1" seconds
  # Make sure I am on the regions page for wales
  Then I should see "Ministry of Justice HQ"
  # Make sure the latest events load
  Then I should see "Events"
  # Make sure there are only no more than 2 event items
  When I am looking at the ".events-widget" component, I should see no more than "2", ".results-item" elements
  # Retrieve the events
  Given I call the intranet API endpoint "/events/hq/10"
  # Check it is displaying HQ events
  Then The first item in the events widget is from "HQ" agency
  When I am looking at the ".events-list li:nth-of-type(1) .title" component, I should see the ".results-link" element
  When I am looking at the ".events-list:nth-of-type(1)" component, I should see the "div.meta" element

Scenario: Test HMTCS Regional 'Events' component Acceptance Criteria
  # Please note this scenario can start failing if the region run out of events,
  # which can happen if the database dump is too old.
  Given I visit "/regional-pages/scotland?agency=hmcts"
  And I pause for "1" seconds
  Then I should see "Scotland"
  Then I should see "Events"
  When I am looking at the ".events-widget" component, I should see no more than "2", ".results-item" elements
  Given I call the intranet API endpoint "/events/hmcts/scotland/10"
  Then The first item in the events widget is from "HMCTS" agency
  When I am looking at the ".events-list" component, I should see the "h3" element
  When I am looking at the ".events-list h3" component, I should see the ".results-link" element
  When I am looking at the ".events-list" component, I should see the "div.meta" element
