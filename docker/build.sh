#!/bin/sh
set -e

###
# Build Script
# Use this script to build theme assets,
# and perform any other build-time tasks.
##

# Clean up the working directory (useful when building from local dev files)
if [ -d ".git" ]
then
	git clean -xdf
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

# Install PHP dependencies (WordPress, plugins, etc.)
composer install --verbose

# Because composer cannot install this in the correct location and does not
# seem to be able to easily move it, itself.
mv vendor/ministryofjustice/mojintranet-theme/wp-content/themes/mojintranet web/app/themes/
rm -rf vendor/ministryofjustice

# Build theme assets
npm install -g grunt-cli
npm install
grunt pre_deploy

# Remove composer auth.json
rm -f auth.json
