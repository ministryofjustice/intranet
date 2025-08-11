#!/usr/bin/env sh

##
# CONFIGURE COMPOSER AUTHENTICATION
#
# This script will create an `auth.json` file, which is used by composer for
# HTTP basic auth access to the private composer repositories:
# composer.deliciousbrains.com & connect.advancedcustomfields.com.
#
# It requires the environment variables: `AS3CF_PRO_USER`, `AS3CF_PRO_PASS`, 
# `ACF_PRO_LICENSE` and, `ACF_PRO_PASS` to be set with authentication credentials.
##

if [ -n "$COMPOSER_TOKEN" ]; then
  composer config --global github-oauth.github.com "$COMPOSER_TOKEN"
fi

if [ -n "$AS3CF_PRO_USER" ] && [ -n "$AS3CF_PRO_PASS" ] && [ -n "$ACF_PRO_LICENSE" ] && [ -n "$ACF_PRO_PASS" ]
then
  rm -f auth.json
	cat <<- EOF >> auth.json
		{
			"http-basic": {
				"composer.deliciousbrains.com": {"username": "$AS3CF_PRO_USER","password": "$AS3CF_PRO_PASS"},
				"connect.advancedcustomfields.com": {"username": "$ACF_PRO_LICENSE", "password": "$ACF_PRO_PASS"}
			}
		}
	EOF
else
	echo "FATAL: AS3CF_PRO_USER, AS3CF_PRO_PASS, ACF_PRO_LICENSE or, ACF_PRO_PASS were not available."
fi

## check for auth.json
if [ ! -f "auth.json" ]; then
  echo "FATAL: auth.json was not written to the FS."
fi
