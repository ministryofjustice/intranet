When(/^I create and populate a blog post titled "([^"]*)"$/) do |value|
  step %Q[I visit "%{WP_ADMIN}/post-new.php?post_type=post"]
  step %Q[I should see "Add New Post"]
  page.find('#title').set(value)
  page.find('#content').set('Forty-two said Deep Thought, with infinite majesty and calm')
  step %Q[I click the "Publish" button]
  step %Q[I should see "Post published."]
  # Leave post so that it is not locked by editor. Ends step on admin homepage.
  step %Q[I visit "%{WP_ADMIN}/wp/wp-admin/index.php"]
end

When(/^I create a post titled "([^"]*)" with comments closed$/) do |value|
  step %Q[I visit "%{WP_ADMIN}/post-new.php?post_type=post"]
  step %Q[I should see "Add New Post"]
  page.find('#title').set(value)
  page.find('#content').set('Forty-two said Deep Thought, with infinite majesty and calm')
  page.find('#comment_status', visible: false).trigger('click')
  step %Q[I click the "Publish" button]
  step %Q[I should see "Post published."]
end
