#!/usr/bin/env ash

source bin/composer-auth.sh

if [ ! -d "./vendor" ]; then
  composer install
fi
