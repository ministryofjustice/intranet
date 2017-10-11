Given(/^I add a new media grid row$/) do
  # The following button does not have a label so targeting it with other attributes
  find('.acf-button[data-event="add-row"]').click
end

And(/^I enter the value "([^"]*)" in the media grid section "([^"]*)"$/) do |value, section|
  within(find("div.acf-field-text[data-name='#{section}']")) do
    find("input[type='text']").set(value)
  end
end

Given(/^I should see an embedded Youtube video with ID "([^"]*)"$/) do |video_id|
  expect(page).to have_css("iframe[src^='https://www.youtube.com/embed/#{video_id}']")
end
