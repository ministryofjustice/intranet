When(/^I visit the preview link$/) do
  @link ||= find('#sample-permalink a')[:href]
  step %[I visit "#{@link}"]
end
