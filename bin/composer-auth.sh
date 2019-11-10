#!/bin/bash

##
# CONFIGURE COMPOSER AUTHENTICATION
#
# This script will create an `auth.json` file, which is used by composer for
# HTTP basic auth access to the private composer repository composer.wp.dsd.io.
#
# It requires the environment variables `COMPOSER_USER` and `COMPOSER_PASS` to
# be set with authentication credentials.
##

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
