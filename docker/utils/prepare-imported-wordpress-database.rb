#!/usr/bin/env ruby

# Run this script when you have a port-forward such that port 3306 on your
# local machine is connected to the database of the environment you want
# to affect.
#
# It will rewrite URLs as part of the migration of the intranet from
# DXW to our AWS stack.
#
# This script is a partial copy of `prepare-local-wordpress-database.rb`
# with minor tweaks to just do the rewrites without affecting anything else.


# Replace these values, as necessary
DOMAIN      = 'intranet.dev.wp.dsd.io'
DB_HOST     = '127.0.0.1'
DB_NAME     = 'intranet'
DB_USER     = 'intranet'
DB_PASSWORD = 'DUMMY_VALUE'

def main
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

  puts `mysql -h#{DB_HOST} -u#{DB_USER} -p#{DB_PASSWORD} #{DB_NAME} --table -e '#{sql.gsub("\n", " ")}'`
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

  `mysql -h#{DB_HOST} -u#{DB_USER} -p#{DB_PASSWORD} #{DB_NAME} -e '#{sql.gsub("\n", " ")}'`
  puts "End SQL fixup"
end

def log(message)
  puts [Time.now.strftime("%Y-%m-%d %H:%M:%S"), message].join(' ')
end

main
