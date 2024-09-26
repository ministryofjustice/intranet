#!/usr/bin/env sh

# Exit with code 1 if any php process has been running for more than 1 hour.
if [ $(ps -o time,comm | grep -c '.*h.*php-fpm') -gt 0 ]; then exit 1; fi
