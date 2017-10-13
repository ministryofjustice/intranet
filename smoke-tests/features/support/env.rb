require 'capybara'
require 'capybara/dsl'
require 'capybara/cucumber'
require 'capybara/poltergeist'
require 'docker-api'
require 'phantomjs'

Capybara.configure do |c|
  c.javascript_driver = :poltergeist
  c.default_driver = :poltergeist
  c.app_host = ENV.fetch('TARGET_URI', 'http://intranet.docker')
  c.run_server = false
  c.default_max_wait_time = 5
end

Capybara.register_driver :poltergeist do |app|
  options = {
    # We don't need this log output for everyday testing and redirecting it to
    # /dev/null stops poltergeist from constantly repeating the JQMIGRATE
    # deprecation warning.
    phantomjs_logger: File.open('/dev/null', 'w'),
    debug: false,
    inspector: true,
    js_errors: false,
    phantomjs: Phantomjs.path,
    phantomjs_options: %w(--load-images=no --disk-cache=false)
  }
  Capybara::Poltergeist::Driver.new(app, options)
end

DOCKER_CONTAINER = ENV.fetch('DOCKER_CONTAINER', 'wp_local_dev').freeze

# The directory where WordPress is installed. Leave blank for none (root)
# Example: if the admin login is '/wp/wp-admin', set this variable to '/wp'
WP_INSTALL_DIRECTORY = ENV.fetch('WP_INSTALL_DIRECTORY', '/wp').freeze
