@skip
Feature: Test HMTCS Regional 'latest' component

Scenario: Test HMTCS Regional 'latest' component Acceptance Criteria
  # Navigate to the Wales regions page for HMCTS
  Given I visit "/regional-pages/wales?agency=hmcts"
  # Make sure I am on the regions page for wales
  Then I should see "Wales"
  # Make sure the latest posts load
  Then I should see "Latest"
  # Make sure there are only no more than 2 news items
  When I am looking at the ".news-list-widget" component, I should see no more than "2", ".news-item" elements
  # Retrieve the news
  Given I call the API endpoint "/regional_news?filter[region]=wales&filter[agency_filter]=hmcts&per_page=1"
  # Check it is displaying a HMCTS post
  Then The first item in the news widget is from "HMCTS" agency
  # Make sure all posts have the correct elements
  When I am looking at the ".news-item" component, I should see the "h3" element
  When I am looking at the ".news-item" component, I should see the "img" element
  When I am looking at the ".news-item" component, I should see the ".excerpt" element
  When I am looking at the ".news-item" component, I should see the ".date" element
