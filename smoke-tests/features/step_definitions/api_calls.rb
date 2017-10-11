Given(/^I call the API endpoint "([^"]*)"$/) do |endpoint|
  api_get(endpoint)
end

Given(/^I call the intranet API endpoint "([^"]*)"$/) do |endpoint|
  api_get(endpoint, namespace: :intranet)
end
