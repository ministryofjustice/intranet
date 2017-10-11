Feature: Agency editor is able to change features on the homepage

Background: Logged in as an agency editor on admin homepage and creates test blog posts
  Given I log into the intranet as "agency_editor"
  Then I should see "Editing as: HQ"

  # Editor creates two posts to use to test homepage features
  When I create and populate a blog post titled "homepage feature test one"
  When I create and populate a blog post titled "homepage feature test two"

Scenario: Agency editor changes the featured stories using Customizer

  # Use Customizer to add blog posts to the two feature item slots
  When I visit "%{WP_ADMIN}/customize.php"
  And I click the "#accordion-section-featured_news" element
  Then I should see "Featured item 1"
  And I should see "Featured item 2"

  # Populating first & second feature items
  When I configure the 1st featured item with Blog Post "homepage feature test one"
  When I configure the 2nd featured item with Blog Post "homepage feature test two"

  # Save added feature items
  And I click the "Save & Publish" button

  # Check the items are on the homepage
  When I visit "/?agency=hq"
  And I pause for "1" seconds
  Then I am looking at the ".featured-news-widget" component, I should see exactly "2", "article" elements
  And I should see "homepage feature test one"
  And I should see "homepage feature test two"

  # Check the items are on not on another agency homepage
  When I visit "/?agency=hmcts"
  And I should not see "homepage feature test one"
  And I should not see "homepage feature test two"

  # Return back to HQ homepage
  When I visit "/?agency=hq"
  And I pause for "1" seconds

  # Back to Customizer to add back old blog posts so testing posts do not remain
  When I visit "%{WP_ADMIN}/customize.php"
  And I click the "#accordion-section-featured_news" element
  Then I should see "Featured item 1"
  And I should see "Featured item 2"
  And I pause for "1" seconds

  # Populating first & second feature items
  When I configure the 1st featured item with Blog Post "A sunny start in Scotland"
  When I configure the 2nd featured item with Blog Post "Ticking all the ‘right’ boxes"

  # Save added feature items
  And I click the "Save & Publish" button

  # Check the items are on the homepage
  When I visit "/?agency=hq"
  And I pause for "1" seconds
  Then I am looking at the ".featured-news-widget" component, I should see exactly "2", "article" elements
  And I should see "A sunny start in Scotland"
  And I should see "Ticking all the ‘right’ boxes"

  # Back to posts to remove test blogs
  # Post one
  When I visit "/blog/homepage-feature-test-one/"
  When I click the "Edit Post" link
  Then I click the "Move to Trash" link
  And I should see "1 post moved to the Trash."
  # Post two
  When I visit "/blog/homepage-feature-test-two/"
  When I click the "Edit Post" link
  Then I click the "Move to Trash" link
  And I should see "1 post moved to the Trash."
