#!/bin/bash
set -e

###
# Build Script
# Use this script to upgrade theme assets.
##

# Install PHP dependencies (WordPress, plugins, etc.)
composer upgrade

if ! [ -x "$(command -v npm-upgrade)" ]; then
  echo 'The command npm-upgrade is required to upgrade node packages. Install this and try again.'
  echo 'npm i -g npm-upgrade'
  exit 1
fi

# Build theme assets
# Supports multiple themes
for d in web/app/themes/*; do
  if [[ -f "$d/package.json" ]]; then
    echo "***"
    echo "Upgrading dependancies in $d/package.json"
    echo "***"
    cd "$d"
    npm-upgrade
    npm install
    npm audit fix
    cd ../../../..
  fi
done

# Remove composer auth.json
rm -f auth.json
