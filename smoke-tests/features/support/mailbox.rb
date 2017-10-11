require 'rest-client'

module SmokeTest
  class MailCatcher
    attr_reader :endpoint, :unique_address

    def initialize(opts)
      @endpoint = opts.fetch(:endpoint, 'http://127.0.0.1:1080/messages')
      @unique_address = opts.fetch(:unique_address)
    end

    def validation_link
      messages = JSON.parse(RestClient.get(endpoint).body)
      # The test uses mailcatcher and UUIDs for the email. username, so there
      # should never be more than one any specific username.  This *does* mean
      # that this method is tightly tied to the commenting link sign in test
      # logic.  This probably needs to be reviewed moving forward.
      message = messages.select{ |m| m['recipients'].include?("<#{unique_address}>") }.first
      parsed_html_message = Nokogiri::HTML(RestClient.get("#{endpoint}/#{message['id']}.html").body)

      # The commenting login email should only contain a single link. This
      # logic will probably need to be changed if the number of links in the
      # mail changes.
      URI.decode(parsed_html_message.css('a').first['href'])
    ensure
      RestClient.delete("#{endpoint}/#{message['id']}")
    end
  end
end
