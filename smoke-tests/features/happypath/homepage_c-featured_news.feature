@skip
Feature: Test homepage featured news component

Scenario: Test homepage featured news for HQ
  Given I visit "/?agency=hq"
  # Make sure I am on HQ
  Then I should see "Ministry of Justice HQ"
  # Make sure the Featured news component has loaded
  When I am looking at the ".featured-news-widget" component, I should see "Featured"
  # Make sure there are only two featured items
  When I am looking at the ".featured-news-widget" component, I should see exactly "2", "article" elements
  # Look for a header element
  When I am looking at the ".featured-news-widget article" component, I should see the "h3" element
  # Look for the post image
  When I am looking at the ".featured-news-widget article" component, I should see the "img" element
  # Look for the post date
  When I am looking at the ".featured-news-widget article" component, I should see the ".meta .date" element
  # Look for the excerpt
  When I am looking at the ".featured-news-widget article" component, I should see the ".news-excerpt" element
  # Retrieve the featured news
  Given I call the intranet API endpoint "/featurednews/hq/1"
  # Check it is displaying HQ featured news
  Then The first item in the featured news widget is from "HQ" agency

Scenario: Test homepage featured news for LAA agency
  Given I visit "/?agency=laa"
  Then I should see "Legal Aid Agency"
  When I am looking at the ".featured-news-widget" component, I should see "Featured"
  Given I call the intranet API endpoint "/featurednews/laa/1"
  Then The first item in the featured news widget is from "LAA" agency
