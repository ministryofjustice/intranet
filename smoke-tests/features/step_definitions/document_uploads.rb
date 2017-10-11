When(/^I open the add document editor$/) do
  visit '%{WP_ADMIN}/post-new.php?post_type=document'
  expect(page).to have_text('Add New Document')
end

When(/^I attach the file "(.*?)"$/) do |filename|
  attach_in_file_uploader(filename)
end

Then(/^A file with name "([^"]*)" should download$/) do |filename|
  expect(page.response_headers['Content-Disposition']).to match(/filename="#{filename}"/)
end

And(/^I switch to the uploader modal window$/) do
  page.switch_to_frame(find(:frame, 'TB_iframeContent'))
end

And(/^I switch to the main window$/) do
  page.switch_to_frame(:top)
end

And(/^I switch to the browser built\-in uploader$/) do
  if page.has_text?('You are using the multi-file uploader.')
    find('a', text: 'browser uploader').click
  end
  expect(page).to have_text('You are using the browser’s built-in file uploader.')
end

And(/^I attach the file "([^"]*)" into the media library$/) do |filename|
  within(find('div.media-modal-content')) do
    input_file = find('input[type="file"]', visible: false)
    attach_in_file_uploader(filename, locator: input_file['id'])
  end
end
