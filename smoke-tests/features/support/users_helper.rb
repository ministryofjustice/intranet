WP_USERS = {
  agency_editor: {
    login: 'agency_editor',
    password: 'password',
    email: 'agency_editor@example.com',
    role: 'agency-editor',
    display_name: 'Agency Editor Test'
  },
  regional: {
    login: 'regional',
    password: 'password',
    email: 'regional@example.com',
    role: 'regional-editor',
    display_name: 'Regional Test'
  }
}.freeze


def get_user(login)
  WP_USERS[login.to_sym]
end

def add_user!(login)
  params = get_user(login)
  login_name = params.fetch(:login)
  delete_user!(login_name)
  create_user!(params)
end

def delete_user!(login)
  container.exec(wp_command(%W[user delete --yes #{login}]))
end

private

def create_user!(params)
  login = params.fetch(:login)
  email = params.fetch(:email)
  role = params.fetch(:role)
  display_name = params.fetch(:display_name)
  password = params.fetch(:password)
  agency = params.fetch(:agency, 'hq')
  region = params.fetch(:region, 'wales')

  result = container.exec(
    wp_command(%W[user create #{login} #{email} --role=#{role} --user_pass=#{password} --display_name=#{display_name} --porcelain])
  )

  # Docker::Container#exec returns values as arrays of arrays where the first
  # array is the response.
  id = result.first.first.strip!

  container.exec(wp_command(%W[user term add #{id} agency #{agency}]))
  container.exec(wp_command(%W[user term add #{id} region #{region}]))
rescue NoMethodError
  # This ensures any `wp create` command errors bubble up. In the event of
  # one, id.first = []. This causes `.first.strip!` to raise NoMethodError.
  puts result
  fail
end

def container
  @_container ||= Docker::Container.get(DOCKER_CONTAINER)
end

# DRY out the wp-cli commands and keep them from getting excessively long
def wp_command(action)
  %w[wp --path=/bedrock/web/wp --allow-root] + action
end
