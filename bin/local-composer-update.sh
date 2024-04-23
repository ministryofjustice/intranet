#!/usr/bin/env ash

source bin/composer-auth.sh

composer update

## check for changes
echo "Checking for changes..."
zip -r -f --quiet vendor vendor
sha1sum -c -s vendor.sha1

## $? = 0 if ok, 1 if not
# remove vendor-assets
if [ $? == "1" ]; then
  rm -rf ./vendor-assets
  sha1sum vendor.zip > vendor.sha1
fi

echo "Done."

