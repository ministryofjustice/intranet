#!/usr/bin/env bash

echo "Running e2e.sh"

function dc-e2e() {
  docker compose --env-file .env.e2e -f docker-compose.yml -f docker-compose.e2e.yml "$@"
}

function fpm-exec() {
  docker compose --env-file .env.e2e -f docker-compose.yml -f docker-compose.e2e.yml exec php-fpm "$@"
}

# - - - - - - - - - - - - - - - - - 
# Stop any running containers
# - - - - - - - - - - - - - - - - - 

dc-e2e stop

# - - - - - - - - - - - - - - - - - - - - - - - - - 
# Start Minio and php-fpm in detached mode.
# Since we need to run initialisation on minio,
# and some WP_CLI commands on php-fpm.
# - - - - - - - - - - - - - - - - - - - - - - - - -

# Start minio in detached mode
dc-e2e up -d minio php-fpm

# - - - - - - - - - - - - - - - - - 
# Initialise Minio
# - - - - - - - - - - - - - - - - - 

# Run the minio-init container to setup the bucket.
dc-e2e up minio-init --exit-code-from minio-init

# Check that it ran successfully, i.e. the exit code was 0.
if [ $? -ne 0 ]; then
  echo "MinIO initialization failed"
  exit 1
fi

echo "✔ Minio initialised"

# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
# Initialise database state with WP_CLI commands
# This is where we set up the database for the e2e tests.
# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

# Reset the database.
fpm-exec bash -c "wp db reset --yes"

# Install WordPress core.
fpm-exec bash -c "wp core install --url=http://intranet.docker --title='MoJ Intranet' --admin_user=admin --admin_password=password  --admin_email=test@gov.uk --skip-email"

# Activate the theme.
fpm-exec bash -c "wp theme activate clarity"

# Enable all plugins.
fpm-exec bash -c "wp plugin activate --all"

# - - - - - -- - - - -
# Taxonomies and terms
# - - - - - -- - - - -

# Create agency terms, these should match the 'is_integrated' agencies in `public/app/themes/clarity/inc/agency.php`
fpm-exec bash -c "wp term create agency CICA   --slug=cica"
fpm-exec bash -c "wp term create agency HMCTS  --slug=hmcts"
fpm-exec bash -c "wp term create agency NOMS   --slug=noms"
fpm-exec bash -c "wp term create agency JO     --slug=judicial-office"
fpm-exec bash -c "wp term create agency LawCom --slug=law-commission"
fpm-exec bash -c "wp term create agency LAA    --slug=laa"
fpm-exec bash -c "wp term create agency MoJ    --slug=hq"
fpm-exec bash -c "wp term create agency OPG    --slug=opg"
fpm-exec bash -c "wp term create agency PB     --slug=pb"
fpm-exec bash -c "wp term create agency OSPT   --slug=ospt"
fpm-exec bash -c "wp term create agency JAC    --slug=jac"

# - - - - - -- - - - -
# User setup
# - - - - - -- - - - -

# Set the agency context for the admin user (ID 1) to HQ.
fpm-exec bash -c "wp user meta add 1 agency_context hq"

# Run the sync-user-roles command to ensure the user roles are in sync.
# This is what is usually run every time a php-fpm container starts.
fpm-exec bash -c "wp sync-user-roles sync"

# Add capabilities to the admin user (ID 1)
# An improvement would be to move this to it's own php file, that is executed as part of the `sync-user-roles` command.
fpm-exec bash -c "wp user add-cap 1 assign_agencies_to_posts"
fpm-exec bash -c "wp user add-cap 1 manage_agencies"

# - - - - - -- - - - -
# Pages and posts
# - - - - - -- - - - -

# Create a page with the slug agency-switcher
fpm-exec bash -c "wp post create --post_type=page --post_title='Agency Switcher' --post_name=agency-switcher --post_status=publish --post_content='This is the agency switcher page' --post_author=1"

# Create homepage and get the ID
HOMEPAGE_ID=$(fpm-exec bash -c "wp post create --post_type=page --post_title='Home' --post_name=home --post_status=publish --post_content='This is the homepage' --post_author=1 --page_template=page_home.php --porcelain")

# - - - - - - - - - - - - - - - - - - - -
# WordPress core settings and preferences
# - - - - - - - - - - - - - - - - - - - -

# Set the homepage to be a static page.
fpm-exec bash -c "wp option set show_on_front 'page'"

# Set the page ID for the homepage.
fpm-exec bash -c "wp option set page_on_front $HOMEPAGE_ID"

# Set the permalink structure.
fpm-exec bash -c "wp option set permalink_structure '/blog/%postname%/'"

echo "✔ Database initialised via WP_CLI"

# - - - - - - - - - - - - - - - - - - - -
# After init, stop all containers, so that the following
# e2e test can start only the services it depends on.
# - - - - - - - - - - - - - - - - - - - -

dc-e2e stop

# - - - - - - - - - - - - - - - - - - - -
# Start the e2e tests
# This will start all the services defined in the docker-compose.e2e.yml file.
# - - - - - - - - - - - - - - - - - - - -

dc-e2e up \
  --build \
  --abort-on-container-exit \
  --exit-code-from e2e \
  --attach e2e

# Check the exit code from the e2e tests.
# If the exit code is not 0, then the tests failed.
if [ $? -ne 0 ]; then
    # If we are here, either the e2e container exited with a non-zero exit code,
    # or one of the other containers exited with a non-zero exit code.
    echo "E2E tests failed"
    exit 1
fi

# If we are here, the e2e container exited with a zero exit code,
# which means the tests passed.
echo "✔ All E2E tests passed"

exit 0
