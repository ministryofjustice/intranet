When(/^I configure the (\d+)(?:st|nd) featured item with (Blog Post) "([^"]*)"$/) do |cardinal, item_type, title|
  # Converts text to an integer (`to_i`) in order to subtract 1 from array and therefore be able to distinguish (as there more than one li item) which one to target.
  position = cardinal.to_i - 1
  customizer_section = page.all('li.customize-control-content_dd')[position]

  within(customizer_section) do
    select(item_type, from: 'Type')
    find('a', text: 'Clear').click
    @autocomplete_field = find('input.ui-autocomplete-input')
  end

  select_from_autocomplete(@autocomplete_field['id'], with: title)

  # sanity check
  expect(find_field(@autocomplete_field['id']).value).to eq(title)
end
