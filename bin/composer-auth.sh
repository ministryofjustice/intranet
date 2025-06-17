#!/usr/bin/env sh

##
# CONFIGURE COMPOSER AUTHENTICATION
#
# This script will create an `auth.json` file, which is used by composer for
# HTTP basic auth access to the private composer repository composer.wp.dsd.io.
#
# It requires the environment variables `COMPOSER_USER` and `COMPOSER_PASS` to
# be set with authentication credentials.
##

if [ -n "$COMPOSER_TOKEN" ]; then
  composer config --global github-oauth.github.com "$COMPOSER_TOKEN"
fi

if [ -n "$COMPOSER_USER" ] && [ -n "$COMPOSER_PASS" ]
then
  rm -f auth.json
	cat <<- EOF >> auth.json
		{
			"http-basic": {
				"composer.wp.dsd.io": {"username": "$COMPOSER_USER","password": "$COMPOSER_PASS"},
				"composer.deliciousbrains.com": {"username": "$AS3CF_PRO_USER","password": "$AS3CF_PRO_PASS"},
				"connect.advancedcustomfields.com": {"username": "$ACF_PRO_LICENSE", "password": "$ACF_PRO_PASS"}
			}
		}
	EOF
else
	echo "FATAL: COMPOSER_USER and COMPOSER_PASS were not available."
fi

## check for auth.json
if [ ! -f "auth.json" ]; then
  echo "FATAL: auth.json was not written to the FS."
fi
