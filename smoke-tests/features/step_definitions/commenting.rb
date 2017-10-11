require_relative '../support/mailbox'
require 'securerandom'

When(/^I am not logged in to comment$/) do
  %Q{Then I expect to see a ".cta" element}
  %Q{Then I should see "Screen name"}
  %Q{Then I should see "Email address"}
end

When(/^I log in using email$/) do
  @test_mail_uuid = SecureRandom.uuid
  test_email = "#{@test_mail_uuid}@digital.justice.gov.uk"
  test_pass = ENV['email_pass']
  expect(page).to have_text('Screen name')
  expect(page).to have_text('Email address')
  fill_in('display_name', with: "Test User #{@test_mail_uuid}")
  fill_in('email', with: test_email)
  click_on('Get link')
  expect(page).to have_text(/check your email/)
  visit(SmokeTest::MailCatcher.new(unique_address: test_email).validation_link)
end

Then(/^I should be logged in to comment$/) do
  expect(page).to have_text("You're posting as Test User #{@test_mail_uuid}")
  expect(page).to have_selector("textarea[placeholder='Enter your comment here...']")
end

And(/^I can post a comment$/) do
  comment_author = "Test User #{@test_mail_uuid}"
  comment_body = 'This is a smoke test comment'

  step %Q[I fill in "comment" with "#{comment_body}"]
  step %Q[I click the "Add comment" button]
  sleep(1) # it takes a bit until the new comment is rendered

  last_comment_element = find('ul.comments-list li.comment:first-of-type')
  @last_comment_id = last_comment_element['data-comment-id']

  within(last_comment_element) do
    expect(page).to have_text("#{comment_author} — a few seconds ago")
    expect(page).to have_text(comment_body)
    expect(page).to have_css('a.reply-btn')
    expect(page).to have_css('a.like-link')
  end
end

Then(/^I delete the posted comment$/) do
  step %Q[I visit "%{WP_ADMIN}/comment.php?action=editcomment&c=#{@last_comment_id}"]
  expect(find_field('newcomment_author').value).to eq("Test User #{@test_mail_uuid}")
  step %Q[I click the "Move to Trash" link]
  step %Q[I should see "1 comment moved to the Trash."]
end
