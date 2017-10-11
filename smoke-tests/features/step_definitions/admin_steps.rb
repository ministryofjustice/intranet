And(/^I quick\-edit the page with title "([^"]*)"$/) do |title|
  step %[I fill in "post-search-input" with "#{title}"]
  step %[I click the "Search Pages" button]

  result_element = find('a', text: title)
  result_element.hover

  parent_element = result_element.find(:xpath, '../..')

  within(parent_element) do
    step %[I click the "Quick Edit" link]
  end
end
