launch:
	docker-compose -f docker-compose-dev.yml up

shutdown:
	docker-compose -f docker-compose-dev.yml down

# Destroy all local images, forcing a rebuild from scratch
nuke-images:
	docker-compose -f docker-compose-dev.yml down --rmi all

# This forces composer and grunt to rebuild their respective files
# the next time the containers are started
nuke-assets:
	rm -rf bedrock_volume/*

nuke-all:
	make nuke-assets
	make nuke-images

load-db-dump:
	docker-compose -f docker-compose-dev.yml exec mysql bash -c 'cat /db-dump/*.sql | mysql -u$${MYSQL_USER} -p$${MYSQL_PASSWORD} $${MYSQL_DATABASE}'

db-console:
	docker-compose -f docker-compose-dev.yml exec mysql bash -c 'mysql -u$${MYSQL_USER} -p$${MYSQL_PASSWORD} $${MYSQL_DATABASE}'

# Restart the S3 filesystem in the Wordpress container. If content was missing from the S3 bucket, and
# then added later (or added to the bucket outside of Wordpress), use this command to make it show up
# on the site.
restart-yas3fs:
	docker-compose -f docker-compose-dev.yml exec wordpress bash -c 'cd /etc/service; sv restart yas3fs'

# Run the script which prepares the Wordpress container for smoke testing, by adding key
# seed data and user accounts
prepare-wordpress-database:
	docker-compose -f docker-compose-dev.yml exec wordpress /utils/prepare-local-wordpress-database.rb

