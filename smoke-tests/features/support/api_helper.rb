require 'hashie/mash'

module ApiHelper
  API_PATHS = {
    default:  '/wp-json/wp/v2',
    intranet: '/wp-json/intranet/v1'
  }.freeze

  def api_get(resource, namespace: :default)
    endpoint = build_endpoint(resource, namespace)
    puts "API endpoint: #{endpoint}"

    response = RestClient.get(endpoint)
    @last_api_response = parse_body(response.body)
  end

  def last_api_response
    @last_api_response || raise('Cached API response not found.')
  end

  private

  def build_endpoint(resource, namespace)
    File.join(Capybara.app_host, API_PATHS.fetch(namespace), resource)
  end

  def parse_body(body)
    Hashie::Mash.new(items: JSON.parse(body))
  end
end

World(ApiHelper)
