#!/bin/bash
set -e

if [ -d ./bedrock/web/app/themes/mojintranet ] && [ ! -z $development ]
then
  cat <<-EOF
    You are running in development mode and it appears that the intranet is
    already installed in your bedrock volume. Clear ./bedrock_volume if you
    want a clean install.
EOF
  exec /sbin/my_init
fi

# Add composer auth file
if [ ! -z $COMPOSER_USER ] && [ ! -z $COMPOSER_PASS ]
then
	cat <<- EOF >> auth.json
		{
			"http-basic": {
				"composer.wp.dsd.io": {
					"username": "$COMPOSER_USER",
					"password": "$COMPOSER_PASS"
				}
			}
		}
	EOF
fi


# Even if the main theme were moved into the path of the host volume
# (./bedrock_volume in this case), the build would still fail when ./bedrock is
# a mounted volume.  This is because a number of dependencies must be installed
# before run and assets must be built.  Doing so in the build phase means that
# the dependencies and assets will disappear when the volume is mounted, which
# always occurs at runtime. Also, we do not want to check built assets and
# external WP plugins into git.

# ADDing the main assets to / and then copying them to ./bedrock, as is done
# below, means that this script can be used to install a container-only version
# as well as a development version that mounts ./bedrock from a host volume.

# Both composer and grunt fail unless these assest are in the ./bedrock directory
mv Gruntfile.js ./bedrock
mv *.json ./bedrock
# WP will not serve requests unless these are in place
mv web/*.php ./bedrock/web
mv config ./bedrock

# Composer can build out-of-context, but the grunt cli switch to change the
# build context does not work as expected. Easiest to just switch to the
# directory to account for this.
cd ./bedrock

# Install PHP dependencies (WordPress, plugins, etc.)
composer install --verbose

# Because composer cannot install this in the correct location and does not
# seem to be able to easily move it, itself. This is most likely because the
# theme is only a part of the overall project.
mv vendor/ministryofjustice/mojintranet-theme/wp-content/themes/mojintranet
web/app/themes/
rm -rf vendor/ministryofjustice

# Build theme assets
npm install -g grunt-cli
npm install
grunt pre_deploy

# Remove composer auth.json
rm -f auth.json
# Remove the composer dependencies
rm composer.json composer.lock bedrock.json moj.json
# Remove the grunt dependencies
rm -rf node_modules Gruntfile.js package.json

# IFF we are running in development mode, set as a standard envrionment
# variable in docker-compose-dev.yml, then this script serves as the CMD
# override and should start the WP container main process when the install is
# finished.
if [ ! -z $development ]
then
  exec /sbin/my_init
fi
