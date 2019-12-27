#!/bin/bash
set -e

###
# Build Script
# Use this script to build theme assets,
# and perform any other build-time tasks.
##

# Install PHP dependencies (WordPress, plugins, etc.)
composer install


# Build theme assets
# ~ enter theme path and uncomment the following commands:

cd web/app/themes/clarity
npm install
npm run dev
rm -rf node_modules
cd ../../../..
