#!/usr/bin/env sh

# Add logging to amazon-s3-and-cloudfront-pro plugin.

# Define the search and replace strings.
AS3CF_FILE=/var/www/html/public/app/plugins/amazon-s3-and-cloudfront-pro/classes/upgrades/upgrade.php
AS3CF_SEARCH="\$this->items_processed++;"
AS3CF_NEW="if(\$upgraded % 25 === 0) { error_log(\"AS3CF_Upgrade - total \$total, items_processed \$this->items_processed, upgrade_name \$this->upgrade_name\"); }"
AS3CF_REPLACE="$AS3CF_SEARCH\n\n\t\t\t$AS3CF_NEW"

# If serach string is in file. Then replace it.
if grep -q  $AS3CF_SEARCH $AS3CF_FILE ; then
  echo "Adding logging to amazon-s3-and-cloudfront-pro..."
  sed -i "s/$AS3CF_SEARCH/$AS3CF_REPLACE/g" $AS3CF_FILE
fi
