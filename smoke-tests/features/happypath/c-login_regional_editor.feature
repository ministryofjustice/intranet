Feature: Test Login as regional editor

  Scenario: Test login as regional editor
    Given I log into the intranet as "regional"
    # I check that I can access all the relevant menu items
    When I am looking at the "#adminmenumain" component, I should see a link which says "Dashboard" and goes to "%{WP_ADMIN}/index.php"
    When I am looking at the "#adminmenumain #menu-posts-regional_page:first-child" component, I should see a link which says "Regional Pages" and goes to "%{WP_ADMIN}/edit.php?post_type=regional_page"
    When I am looking at the "#adminmenumain #menu-posts-regional_news:first-child" component, I should see a link which says "Regional News" and goes to "%{WP_ADMIN}/edit.php?post_type=regional_news"
    When I am looking at the "#adminmenumain" component, I should see a link which says "Media" and goes to "%{WP_ADMIN}/upload.php"
    When I am looking at the "#adminmenumain #menu-posts-event:first-child" component, I should see a link which says "Events" and goes to "%{WP_ADMIN}/edit.php?post_type=event"
    When I am looking at the "#adminmenumain #menu-posts-document:first-child" component, I should see a link which says "Documents MoJ" and goes to "%{WP_ADMIN}/edit.php?post_type=document"
    When I am looking at the "#adminmenumain" component, I should see a link which says "Profile" and goes to "%{WP_ADMIN}/profile.php"
