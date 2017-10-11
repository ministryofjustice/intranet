Feature: Test Search bar

Scenario: Test Search bar [HQ] Acceptance Criteria

  # Go to the HMCTS homepage
  Given I visit "/?agency=hmcts"
  # Check the search field exists
  Then I expect to see a ".search-form" element
  # Search for something
  Then I fill in "s" with "hello"
  Then I click the ".search-btn" element
  Then I should be on "/search-results/all/hello/1/"
  Then I expect the ".header-search" element to be hidden
  # Keyword search field containing previously entered [search text] appears in page
  Then The field with selector ".search-form .keywords-field" should contain the value "hello"
  # * List of matching posts and documents are displayed, containing the following:
  When I am looking at the ".results .search-item" component, I should see the ".title" element
  When I am looking at the ".results .search-item" component, I should see the ".date" element
  When I am looking at the ".results .search-item" component, I should see the ".excerpt" element
  When I am looking at the ".results .search-item" component, I should see the ".type" element
  Then If ".results .search-item .file" exists, I should see the ".file-link" element
  Then If ".results .search-item .file" exists, I should see the ".file-size" element
  Then If ".results .search-item .file" exists, I should see the ".file-length" element
