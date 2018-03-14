#!/bin/bash

set -e

cat <<- EOF
################################################################################
# Waiting for Wordpress Postmeta table to become available.
#
# On dockerized development environments it can take 30+ seconds for the mysql
# container to populate the wordpress database. While this is happening, users
# will see database connection failures or odd wordpress layout failures. This
# script is intended to keep wordpress container from fully starting until the
# wp_postmeta table is available.  By the time this happens, the database will
# *probably* be fully usable.  If wordpress is responding, but there are issues
# with the layout, please wait another 10 to 15 seconds; the wordpress database
# may not be fully loaded.
################################################################################

EOF

sql='SELECT COUNT(*) FROM wordpress.wp_postmeta'
until mysql -h"$DB_HOST" -p"$DB_PASSWORD" -u"$DB_USER" -e"$sql" 2>&1 | grep -q COUNT; do
    >&2 echo "Wordpress Postmeta table is unavailable - sleeping"
    sleep 1
done

cat <<- EOF
################################################################################
# Wordpress Postmeta table is now available. Continuing.
################################################################################

EOF

exec /usr/bin/supervisord
