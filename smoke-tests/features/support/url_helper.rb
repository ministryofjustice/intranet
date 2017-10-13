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
      WP_DIR: WP_INSTALL_DIRECTORY,
      WP_ADMIN: [WP_INSTALL_DIRECTORY, 'wp-admin'].join('/')
    }
  end
end

World(UrlHelper)
