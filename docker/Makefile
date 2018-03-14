# StatusChecker is a yas3fs report that runs once every five seconds, cannot be disabled, and floods the logs
# with unnecessary information.
launch: download_latest_db_dump
	docker-compose -f ../docker-compose-dev.yml up --abort-on-container-exit --remove-orphans | grep -v StatusChecker

download_latest_db_dump:
ifeq (,$(wildcard ../db-dump/intranet.sql.gz))
	aws s3 cp s3://moj-intranet-smoketest-sql/latest/intranet.sql.gz ../db-dump
endif

rebuild:
	docker-compose -f ../docker-compose-dev.yml up --abort-on-container-exit --remove-orphans --build | grep -v StatusChecker

shutdown:
	docker-compose -f ../docker-compose-dev.yml down

# Destroy all local images, forcing a rebuild from scratch
nuke-images:
	docker-compose -f ../docker-compose-dev.yml down --rmi all

# This forces composer and grunt to rebuild their respective files
# the next time the containers are started
nuke-assets:
	echo 'Deprecated'

# Removes the database volume as well.
nuke-all:
	docker-compose -f ../docker-compose-dev.yml down --rmi all -v

db-console:
	docker-compose -f ../docker-compose-dev.yml exec mysql bash -c 'mysql -u$${MYSQL_USER} -p$${MYSQL_PASSWORD} $${MYSQL_DATABASE}'

# Restart the S3 filesystem in the Wordpress container. If content was missing from the S3 bucket, and
# then added later (or added to the bucket outside of Wordpress), use this command to make it show up
# on the site.
restart-yas3fs:
	docker-compose -f ../docker-compose-dev.yml exec wordpress bash -c 'cd /etc/service; sv restart yas3fs'

# Run the script which prepares the Wordpress container for smoke testing, by adding key
# seed data and user accounts
prepare-wordpress-database:
	echo 'Deprecated'

download_latest_smoketest_dump:
ifeq (,$(wildcard ../smoketest-db-dump/intranet.sql.gz))
	mkdir -p ../smoketest-db-dump
	aws s3 cp s3://moj-intranet-smoketest-sql/latest/intranet.sql.gz ../smoketest-db-dump
endif

smoketest: download_latest_smoketest_dump
	docker-compose -f ../docker-compose-smoketest.yml up --abort-on-container-exit --remove-orphans --build
	rm -rf ../smoketest-db-dump

smoketest-rebuild: smoketest

smoketest-nuke-all:
	docker-compose -f ../docker-compose-smoketest.yml down --rmi all -v
	rm -rf ../smoketest-db-dump
