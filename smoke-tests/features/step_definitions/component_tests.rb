When(/^I am looking at the "([^"]*)" component, I should see the "([^"]*)" elements?$/) do |component, element|
  page.all(component).each do |el|
    expect(el).to have_css(element)
  end
end

When(/^I am looking at the "([^"]*)" component, I should see exactly "([^"]*)", "([^"]*)" elements?$/) do |component, count, element|
  page.all(component).each do |el|
    expect(el).to have_css(element, count: count)
  end
end

When(/^I am looking at the "([^"]*)" component, I should see no more than "([^"]*)", "([^"]*)" elements?$/) do |component, count, element|
  page.all(component).each do |el|
    expect(el).to have_css(element, maximum: count)
  end
end

When(/^I am looking at the "([^"]*)" component, I should see "([^"]*)"$/) do |component, text|
  page.find(component, text: text)
end

When(/^I am looking at the "([^"]*)" component, I should see a link which says "(.*?)" and goes to "(.*?)"$/) do |component, text, url|
  page.all(component).each do |el|
    expect(el.find_link(text)[:href]).to match(url_with_substitutions(url))
  end
end

# TODO: How to ensure a returned post through the API is from a given agency? Sometimes, they can match.
And(/^The first item in the (blog|news) widget is from "([^"]*)" agency$/) do |post_type, _agency|
  selectors = {
    'blog'   => '.posts-widget .results-item h3',
    'news'   => '.news-list-widget li h3'
  }.freeze

  rendered_title = page.all(selectors[post_type]).first.text.gsub('’', "'")
  expected_title = last_api_response.items.first.title.rendered.gsub('’', "'")
  expect(rendered_title).to eq(expected_title)
end

# The JSON structure for the `intranet` endpoints is different from the WP endpoints
And(/^The first item in the (featured news|homepage news|homepage blog|events) widget is from "([^"]*)" agency$/) do |post_type, _agency|
  selectors = {
    'events' => '.events-widget li h3',
    'featured news' => '.featured-news-widget li h3.title',
    'homepage news' => '.news-list-widget li h3',
    'homepage blog' => '.posts-widget .posts-list li h3'
  }.freeze

  # Workaround, as the API doesn't seem to be returning the events in the correct order when
  # the event lasts for a timeframe (like a month) and the start date is already in the past.
  last_api_response.items.sort_by!{ |item| item.event_start_date }

  rendered_title = page.all(selectors[post_type]).first.text.gsub('’', "'")
  expected_title = last_api_response.items.first.post_title.gsub('’', "'")
  expect(rendered_title).to eq(expected_title)
end

And(/^The items in the (blog|news) widget are displayed in reverse date order$/) do |post_type|
  selectors = {
    'blog' => '.posts-widget .results-item .post-date',
    'news' => '.news-list-widget li .date'
  }.freeze

  dates = page.all(selectors[post_type]).map(&:text)
  comparison_result = Date.parse(dates.first) >= Date.parse(dates.last)
  expect(comparison_result).to eq(true)
end

Then(/^The (\d+)(?:st|nd|rd|th) item in the expanded left menu should be "([^"]*)"$/) do |cardinal, text|
  position = cardinal.to_i - 1
  menu_items = page.all('li.menu-item.current ul.children-list li')
  expect(menu_items[position].text).to eq(text)
end
