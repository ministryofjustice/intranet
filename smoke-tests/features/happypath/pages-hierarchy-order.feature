Feature: Pages left hand navigation order
  # This test is very coupled to the specific hierarchy of the page
  # used in the test. If it ever changes, the test will break.

Background:
  # Order the background as the test expects
  When I log into the intranet as "agency_editor"
  When I visit "%{WP_ADMIN}/edit.php?post_type=page"
  Then I quick-edit the page with title "Working with private offices"
  And I fill in "menu_order" with "0"
  And I click the ".button-primary.save" element

Scenario: Test paged left hand navigation can be re-ordered
  Given I visit "/about-us/ministers-and-parliament/?agency=hq"
  And I pause for "1" second

  # Ensure we start with the default sorting order (alphabetical)
  Then The 1st item in the expanded left menu should be "Ministers"
  And  The 2nd item in the expanded left menu should be "Parliament"
  And  The 3rd item in the expanded left menu should be "Working with private offices"

  # Login and change the order
  And I visit "%{WP_ADMIN}/edit.php?post_type=page"
  Then I quick-edit the page with title "Working with private offices"
  And I fill in "menu_order" with "1"
  And I click the ".button-primary.save" element

  # Check the new order
  When I visit "/about-us/ministers-and-parliament/?agency=hq"
  And I pause for "1" second
  Then The 1st item in the expanded left menu should be "Working with private offices"
  And  The 2nd item in the expanded left menu should be "Ministers"
  And  The 3rd item in the expanded left menu should be "Parliament"

  # Revert back to the original order
  When I visit "%{WP_ADMIN}/edit.php?post_type=page"
  Then I quick-edit the page with title "Working with private offices"
  And I fill in "menu_order" with "0"
  And I click the ".button-primary.save" element

  # Ensure we end up with the default sorting order (alphabetical)
  When I visit "/about-us/ministers-and-parliament/?agency=hq"
  And I pause for "1" second
  Then The 1st item in the expanded left menu should be "Ministers"
  And  The 2nd item in the expanded left menu should be "Parliament"
  And  The 3rd item in the expanded left menu should be "Working with private offices"
