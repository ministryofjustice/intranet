default: build

# Run the project build script
build:
	bin/build.sh

# Remove ignored git files – e.g. composer dependencies and built theme assets
# But keep .env file, .idea directory (PhpStorm config), and uploaded media files
clean:
	@if [ -d ".git" ]; then git clean -xdf --exclude ".env" --exclude ".idea" --exclude "web/app/uploads"; fi

# Remove all ignored git files (including media files)
deep-clean:
	@if [ -d ".git" ]; then git clean -xdf --exclude ".idea"; fi

# Remove ALL docker images on the system
docker-clean:
	bin/docker-clean.sh

# Run the application
run:
	docker-compose up

# Open a bash shell on the running container
bash:
	docker-compose exec wordpress bash

# Within docker (run bash first); process a db import on the first .sql file found in the current directory
# Maybe add an admin user if needed
db:
	bin/local-db-import.sh

# Run tests
test:
	composer test

# Fix tests
test-fixes:
	composer test-fix
