@skip
Feature: Test homepage news component

Scenario: Test homepage news for HQ
  Given I visit "/?agency=hq"
  # Make sure I am on HQ
  Then I should see "Ministry of Justice HQ"
  # Introducing a pause as it seems to take a while sometimes to load the widget
  And I pause for "1" seconds
  # Make sure the news component has loaded
  When I am looking at the ".news-list-widget" component, I should see "News"
  # Make sure there are only no more than 10 news items
  When I am looking at the ".news-list-widget" component, I should see no more than "10", "article" elements
  # Look for a header element
  When I am looking at the ".news-list-widget article" component, I should see the "h3" element
  # Look for the post image
  When I am looking at the ".news-list-widget article" component, I should see the "img" element
  # Look for the post date
  When I am looking at the ".news-list-widget article" component, I should see the ".date" element
  # Retrieve the news
  Given I call the intranet API endpoint "/homenews/hq/1"
  # Check it is displaying HQ news
  Then The first item in the homepage news widget is from "HQ" agency
  # Check that the news are displaying in reverse date order
  And The items in the news widget are displayed in reverse date order

Scenario: Test homepage news for LAA agency
  Given I visit "/?agency=laa"
  Then I should see "Legal Aid Agency"
  And I pause for "1" seconds
  When I am looking at the ".news-list-widget" component, I should see "News"
  Given I call the intranet API endpoint "/homenews/laa/1"
  Then The first item in the homepage news widget is from "LAA" agency
  And The items in the news widget are displayed in reverse date order
