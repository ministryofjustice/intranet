Then(/^I log into the intranet as "([^"]*)"$/) do |username|
  password = ENV.fetch("pass_#{username}")

  # If the number of users were to grow too much, probably better to
  # extract to some other place, or even env variables. For now until we
  # know, this is a middle ground solution.
  greeting = {
    'agency_editor' => 'Howdy, Agency Editor Test',
    'regional' => 'Howdy, regional'
  }.fetch(username)

  step %Q[I visit "%{WP_ADMIN}"]
  step %Q[I fill in "user_login" with "#{username}"]
  step %Q[I fill in "user_pass" with "#{password}"]
  step %Q[I click the "Log In" button]

  expect(page).to have_text(greeting)
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
