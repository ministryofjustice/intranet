Then(/^I check my current uri/) do
  puts "Current URI: #{current_url}"
end

Then(/^I save and open the current page/) do
  save_and_open_page
end

Then(/^I pause for debugging$/) do
  require 'pry'
  binding.pry
end
