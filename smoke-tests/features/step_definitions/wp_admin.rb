Then(/^I log into the intranet as "([^"]*)"$/) do |username|
  user_details = get_user(username)

  step %Q[I visit "%{WP_ADMIN}"]
  step %Q[I fill in "user_login" with "#{user_details[:login]}"]
  step %Q[I fill in "user_pass" with "#{user_details[:password]}"]
  step %Q[I click the "Log In" button]

  expect(page).to have_text("Howdy, #{user_details[:display_name]}")
end

When(/^I open the new page editor$/) do
  visit '%{WP_ADMIN}/post-new.php?post_type=page'
  expect(page).to have_text('Add New Page')
end

When(/^I create a new post$/) do
  visit '%{WP_ADMIN}/post-new.php?post_type=post'
  expect(page).to have_text('Add New Post')
end

Then(/^I change the current template to "([^"]*)"$/) do |template_name|
  select template_name, from: 'page_template'
end
