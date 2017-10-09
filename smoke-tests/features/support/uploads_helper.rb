module UploadsHelper
  FIXTURES_PATH = "#{File.dirname(__FILE__)}/../uploads"

  def attach_in_file_uploader(filename, locator: 'async-upload')
    attach_file(locator, "#{FIXTURES_PATH}/#{filename}", make_visible: true)
  end
end

World(UploadsHelper)
