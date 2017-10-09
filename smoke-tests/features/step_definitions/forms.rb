When(/^I fill in "([^"]*)" with "([^"]*)"$/) do |field, value|
  fill_in(field, with: value)
end

When(/^I fill the autocomplete "([^"]*)" with "([^"]*)"$/) do |field, value|
  fill_in_autocomplete(field, with: value)
end

When(/^I fill in the field with the selector "(.*?)" with "([^"]*)"$/) do |element, value|
  page.find(element).set(value)
end

When(/^The fields? with selector "(.*?)" should contain the value "([^"]*)"$/) do |element, value|
  expect(page.find(element).value()).to eq(value)
end

When(/^The fields? with selector "(.*?)" should not contain the value "([^"]*)"$/) do |element, value|
  expect(page.find(element).value()).not_to eq(value)
end

When(/^I choose "([^"]*)"$/) do |text|
  step %[I click the radio button "#{text}"]
  find('[name=commit]').click
end

When(/^I click the radio button "([^"]*)"$/) do |text|
  find('label', text: text, visible: false).trigger(:click)
end
