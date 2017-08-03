launch:
	docker-compose -f docker-compose-dev.yml up

shutdown:
	docker-compose -f docker-compose-dev.yml down

# Destroy all local images, forcing a rebuild from scratch
nuke-images:
	docker-compose -f docker-compose-dev.yml down --rmi all

# This forces composer and grunt to rebuild their respective files
nuke-assets:
	rm -rf bedrock_volume/*
