Feature: Test Login as agency editor

  @pending
  Scenario: Test Login as agency editor
    Given I log into the intranet as "agency_editor"
    # I check that I can access all the relevant menu items
    When I am looking at the "#adminmenumain" component, I should see a link which says "Dashboard" and goes to "%{WP_ADMIN}/index.php"
    When I am looking at the "#adminmenumain #menu-posts-news:first-child" component, I should see a link which says "News Stories" and goes to "%{WP_ADMIN}/edit.php?post_type=news"
    When I am looking at the "#adminmenumain #menu-posts:first-child" component, I should see a link which says "Posts" and goes to "%{WP_ADMIN}/edit.php"
    When I am looking at the "#adminmenumain" component, I should see a link which says "Media" and goes to "%{WP_ADMIN}/upload.php"
    When I am looking at the "#adminmenumain #menu-pages:first-child" component, I should see a link which says "Pages" and goes to "%{WP_ADMIN}/edit.php?post_type=page"
    When I am looking at the "#adminmenumain #menu-posts-webchat:first-child" component, I should see a link which says "Webchats" and goes to "%{WP_ADMIN}/edit.php?post_type=webchat"
    When I am looking at the "#adminmenumain #menu-posts-event:first-child" component, I should see a link which says "Events" and goes to "%{WP_ADMIN}/edit.php?post_type=event"
    When I am looking at the "#adminmenumain #menu-posts-document:first-child" component, I should see a link which says "Documents MoJ" and goes to "%{WP_ADMIN}/edit.php?post_type=document"
    When I am looking at the "#adminmenumain" component, I should see a link which says "Profile" and goes to "%{WP_ADMIN}/profile.php"
    When I am looking at the "#adminmenumain #toplevel_page_quick-links-settings:first-child" component, I should see a link which says "Quick Links" and goes to "%{WP_ADMIN}/admin.php?page=quick-links-settings"
    When I am looking at the "#adminmenumain #toplevel_page_guidance-most-visted-settings:first-child" component, I should see a link which says "Guidance Most Visited" and goes to "%{WP_ADMIN}/admin.php?page=guidance-most-visted-settings"
    When I am looking at the "#adminmenumain #toplevel_page_customize:first-child" component, I should see a link which says "Customise" and goes to "%{WP_ADMIN}/customize.php"
