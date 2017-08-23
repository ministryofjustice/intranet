#!/usr/bin/env ruby

DOMAIN = ENV.fetch('INTRANET_DOMAIN', 'http://intranet.docker')

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
    email: 'regional@example.com',
    role: 'regional-editor',
    password: ENV.fetch('pass_regional'),
    display_name: 'regional'
  )

  display_substitution_counts("Starting count of required changes:")
  run_sql_to_fix_images_and_links()
  display_substitution_counts("Ending count of required changes (all should be 0):")
end

def display_substitution_counts(message)
  puts "#################################################"
  puts message
  sql = <<~SQL
  SELECT COUNT(*) AS wp_options_changes FROM wp_options WHERE option_value LIKE "%https://s3-eu-west-1.amazonaws.com/moj-wp-prod/wp-content/%" AND option_name LIKE "%need_to_know_image%";
  SELECT COUNT(*) AS wp_posts_guid_changes FROM wp_posts WHERE guid LIKE "%https://intranet.justice.gov.uk/wp-content/%";
  SELECT COUNT(*) AS wp_posts_post_content_relative_link_changes FROM wp_posts WHERE post_content LIKE "%https://intranet.justice.gov.uk/%";
  SELECT COUNT(*) AS wp_posts_post_content_remove_s3_uri FROM wp_posts WHERE post_content LIKE "%https://s3-eu-west-1.amazonaws.com/moj-wp-prod/wp-content%";
  SELECT COUNT(*) AS wp_postmeta_meta_value_relative_link_changes FROM wp_postmeta WHERE meta_value LIKE "%https://intranet.justice.gov.uk%";
  SQL

  puts `mysql -h${DB_HOST} -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME} --table -e '#{sql.gsub("\n", " ")}'`
  puts "Count finished"
  puts "#################################################"
end

def run_sql_to_fix_images_and_links
  puts "Start SQL fixup"
  puts "Using domain: #{DOMAIN}"
  sql = <<~SQL
  UPDATE wp_options SET option_value = replace(option_value, "https://s3-eu-west-1.amazonaws.com/moj-wp-prod/wp-content/", "#{DOMAIN}/app/") WHERE option_name LIKE "%need_to_know_image%";
  UPDATE wp_posts SET guid = replace(guid, "https://intranet.justice.gov.uk/wp-content/", "#{DOMAIN}/app/");
  UPDATE wp_posts SET post_content = replace(post_content, "https://intranet.justice.gov.uk/", "/");
  UPDATE wp_posts SET post_content = replace(post_content, "https://s3-eu-west-1.amazonaws.com/moj-wp-prod/wp-content", "/app");
  UPDATE wp_postmeta SET meta_value = replace(meta_value,"https://intranet.justice.gov.uk/","/");
  SQL

  `mysql -h${DB_HOST} -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME} -e '#{sql.gsub("\n", " ")}'`
  puts "End SQL fixup"
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
  id = `cd /bedrock/web; wp --allow-root user create #{login} #{email} --role=#{role} --user_pass='#{password}' --display_name='#{display_name}' --porcelain 2>/dev/null`
  puts "ID is #{id}"
  id.strip!
  system "cd /bedrock/web; wp --allow-root user term add #{id} agency hq 2>/dev/null"
  system "cd /bedrock/web; wp --allow-root user term add #{id} region wales 2>/dev/null"

end

def log(message)
  puts [Time.now.strftime("%Y-%m-%d %H:%M:%S"), message].join(' ')
end

main
