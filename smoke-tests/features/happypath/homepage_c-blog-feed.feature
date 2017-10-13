@skip
Feature: Test homepage blog component

Scenario: Test Blog feed for HQ
  Given I visit "/?agency=hq"
  # Make sure I am on HQ
  Then I should see "Ministry of Justice HQ"
  # Make sure the Blog component has loaded
  When I am looking at the ".posts-widget" component, I should see "Blog"
  # Look for a header element
  When I am looking at the ".posts-widget .results-item" component, I should see the "h3" element
  # Look for the author image
  When I am looking at the ".posts-widget .results-item" component, I should see the "img" element
  # Look for the post date
  When I am looking at the ".posts-widget .results-item" component, I should see the ".post-date" element
  # Retrieve the blog posts
  Given I call the intranet API endpoint "/homebloglist/hq/1"
  # Check it is displaying a HQ post
  Then The first item in the homepage blog widget is from "HQ" agency
  # Check that the posts are displaying in reverse date order
  And The items in the blog widget are displayed in reverse date order

Scenario: Test Blog feed for LAA agency
  Given I visit "/?agency=laa"
  Then I should see "Legal Aid Agency"
  When I am looking at the ".posts-widget" component, I should see "Blog"
  Given I call the intranet API endpoint "/homebloglist/laa/1"
  Then The first item in the homepage blog widget is from "LAA" agency
  And The items in the blog widget are displayed in reverse date order
