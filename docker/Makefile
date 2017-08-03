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

load-db-dump:
	docker-compose -f docker-compose-dev.yml exec mysql bash -c 'cat /db-dump/*.sql | mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE}'`
