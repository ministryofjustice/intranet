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
	cp .env.example .env
	docker-compose up

# Stop the application
down:
	docker-compose down

# Launch the application, open browser, no stdout
launch:
	bin/launch.sh

# Open a bash shell on the running container
bash:
	docker-compose exec wordpress bash

# Run tests
test:
	composer test

# Fix tests
test-fixes:
	composer test-fix
