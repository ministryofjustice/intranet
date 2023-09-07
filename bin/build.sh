#!/bin/bash
set -e

# Build Script
# Use this script to build theme assets,
# and perform any other build-time tasks.

# Install PHP dependencies (WordPress, plugins, etc.)
if [[ "$WP_ENV" == "production" ]]
then
  composer install --no-dev
else
  composer install
fi

# Build theme assets
cd web/app/themes/clarity
npm install
npm run "$WP_ENV"
rm -rf node_modules
cd ../../../..
