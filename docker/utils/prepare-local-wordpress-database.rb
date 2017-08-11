#!/usr/bin/env ruby

# TODO: this is tightly coupled to the smoke tests, and the test data
# they're expecting to be present. This needs to be managed, somehow.
def main

  # TODO: BEFORE COMMITING
  # replace the passwords with ENV.fetch('something')
  # in the docker-compose-dev.yml, set the env vars

  ensure_user_exists(
    login: 'agency_editor',
    email: 'agency_editor@example.com',
    role: 'agency-editor',
    password: 'dummy-value',
    display_name: 'Agency Editor Test'
  )

  ensure_user_exists(
    login: 'regional',
    email: 'regional@example.com',
    role: 'regional-editor',
    password: 'dummy-value',
    display_name: 'regional'
  )

end

# The smoke tests expect certain users, some of which have been created
# in production, but with passwords that differ from those configured in
# the smoke tests. So, delete and recreate any user accounts we depend on.
def ensure_user_exists(params)
  login = params.fetch(:login)
  if user_exists?(login)
    log("User #{login} exists. Recreating")
    delete_user(login)
  end
  create_user(params) unless user_exists?(login)
end

def user_exists?(login)
  system "cd /bedrock/web; wp --allow-root user get #{login} > /dev/null 2>&1"
end

def delete_user(login)
  system "cd /bedrock/web; wp --allow-root user delete --yes #{login} > /dev/null 2>&1"
end

# Create a user.
# WARNING: This will break if any of the params contain a single quote
def create_user(params)
  log "Creating user #{params.fetch(:login)} with role #{params.fetch(:role)}"
  login = params.fetch(:login)
  email = params.fetch(:email)
  role = params.fetch(:role)
  password = params.fetch(:password)
  display_name = params.fetch(:display_name)
  system "cd /bedrock/web; wp --allow-root user create #{login} #{email} --role=#{role} --user_pass='#{password}' --display_name='#{display_name}' 2>/dev/null"
end

def log(message)
  puts [Time.now.strftime("%Y-%m-%d %H:%M:%S"), message].join(' ')
end

main
