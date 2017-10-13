# See `dropzone_helpers.rb` for the step relating to checkboxes and a comment
# explaining why that step is there.
require 'rest-client'
include SelectHelper

Given(/^I show my environment$/) do
  puts "Running against: #{Capybara.app_host}"
end

When(/^I visit "(.*?)"$/) do |path|
  # https://github.com/teampoltergeist/poltergeist/issues/719
  page.driver.clear_memory_cache
  visit path
end

Then(/^I should be on "([^"]*)"$/) do |page_name|
  expect("#{Capybara.app_host}#{URI.parse(current_url).path}").to eql("#{Capybara.app_host}#{page_name}")
end

Then(/^I should see "(.*?)"$/) do |text|
  expect(page).to have_text(text)
end

#Make sure the page matches a regex
Then(/^The content should contain "([^"]*)"$/) do |regex|
  expect(page.text).to match(Regexp.new(regex))
end

Then(/^I should not see "(.*?)"$/) do |text|
  expect(page).not_to have_text(text)
end

And(/^I should( not)? see "([^"]*)" as the post author$/) do |negate, text|
  within(find('div.byline')) do
    negate ? expect(page).not_to(have_text(text)) : expect(page).to(have_text(text))
  end
end

Then(/^I should see an image called "(.*?)"$/) do |path|
  expect(page).to have_css("img[src*='#{path}']")
end

Then(/^I should see a link which says "(.*?)" and goes to "(.*?)"$/) do |text, url|
  expect(find_link(text)[:href]).to match(url)
end

Then(/^I expect to see a "([^"]*)" element$/) do |element|
  page.find(element)
end

Then(/^I should see the page title "([^"]*)"$/) do |text|
  expect(page).to have_selector('h1.page-title', text: text)
end

Then(/^I should not see a "([^"]*)" element$/) do |element|
  expect(page).to have_no_selector(element, visible: false)
end

Then(/^If "([^"]*)" exists, I should see the "([^"]*)" elements?$/) do |haystack, needle|
  page.all(haystack).each do |hs|
    expect(hs.find(needle)).not_to be_blank
  end
end

Then(/^I expect the "([^"]*)" elements? to be hidden$/) do |element|
  expect(page).not_to have_css(element, visible: :hidden)
end

When(/^I click the "(.*?)" element$/) do |element|
  page.find(element).click
end

When(/^I click the "(.*?)" link$/) do |text|
  find("a", :text => "#{text}").click
end

When(/^I click the "(.*?)" button$/) do |text|
  begin
    find("input[value='#{text}']").click
  rescue Capybara::Poltergeist::MouseEventFailed
    find("input[value='#{text}']").trigger('click')
  end
end

When(/^I click the hidden "(.*?)" element$/) do |element|
  find(element, visible: false).trigger(:click)
end

When(/^I click the hidden "(.*?)" element and accept the confirmation$/) do |element|
  accept_confirm { step %[I click the hidden "#{element}" element] }
end

When(/^I hover over the "(.*?)" element$/) do |element|
  find(element).hover
end

When(/^I hover on "(.*?)"$/) do |text|
    find("a", :text => "#{text}").hover
end

Given(/^I fill in my email address$/) do
  @email ||= "#{SecureRandom.uuid}@test.com"
  puts @email
  step %[I fill in "Your email address" with "#{@email}"]
end

When(/^I pause for "([^"]*)" seconds?$/) do |seconds|
  sleep seconds.to_i
end

And(/^The current URL path is "([^"]*)"$/) do |path|
  expect(page).to have_current_path(path, only_path: true)
end

And(/^I switch to the last window opened$/) do
  switch_to_window(windows.last)
end

Given(/^I logout$/) do
  step %[I visit "%{WP_DIR}/wp-login.php?action=logout"]
  find('a', text: 'log out').click
end
