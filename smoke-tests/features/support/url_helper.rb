module UrlHelper
  def visit(url)
    super url_with_substitutions(url)
  end

  def url_with_substitutions(url)
    url % substitutions
  end

  private

  def substitutions
    @_substitutions ||= {
      WP_DIR: wp_install_directory,
      WP_ADMIN: [wp_install_directory, 'wp-admin'].join('/')
    }
  end

  def wp_install_directory
    ENV.fetch('WP_INSTALL_DIRECTORY')
  end
end

World(UrlHelper)
