#!/usr/bin/env ruby

def main
  ensure_user_exists(
    login: 'agency_editor',
    email: 'agency_editor@example.com',
    role: 'agency-editor',
    password: ENV.fetch('pass_agency_editor'),
    display_name: 'Agency Editor Test'
  )

  ensure_user_exists(
    login: 'regional',
    email: 'mojintranettest+regional@gmail.com',
    role: 'regional-editor',
    password: ENV.fetch('pass_regional'),
    display_name: 'regional'
  )

  ensure_user_exists(
    login: 'admin',
    email: 'admin@example.com',
    role: 'administrator',
    password: ENV.fetch('pass_admin'),
    display_name: 'Administrator'
  ) if ENV['pass_admin']
end

# The smoke tests expect certain users, some of which have been created
# in production, but with passwords that differ from those configured in
# the smoke tests. So, update the passwords of users we depend on.
def ensure_user_exists(params)
  login = params.fetch(:login)
  if user_exists?(login)
    log("User #{login} exists. Updating password.")
    update_user_password(params)
  end
  create_user(params) unless user_exists?(login)
end

def user_exists?(login)
  system "cd /bedrock/web; wp --allow-root user get #{login} > /dev/null 2>&1"
end

def update_user_password(params)
  login = params.fetch(:login)
  password = params.fetch(:password)
  system "cd /bedrock/web; wp --allow-root user update #{login} --user_pass='#{password}' > /dev/null 2>&1"
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
