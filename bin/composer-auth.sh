#!/usr/bin/env ash

##
# CONFIGURE COMPOSER AUTHENTICATION
#
# This script will create an `auth.json` file, which is used by composer for
# HTTP basic auth access to the private composer repository composer.wp.dsd.io.
#
# It requires the environment variables `COMPOSER_USER` and `COMPOSER_PASS` to
# be set with authentication credentials.
##

if [ -n "$COMPOSER_USER" ] && [ -n "$COMPOSER_PASS" ]; then
  composer config --global github-oauth.github.com "$COMPOSER_TOKEN"
  rm -f auth.json
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
