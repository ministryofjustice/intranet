#!/usr/bin/env sh

# Add logging to amazon-s3-and-cloudfront-pro plugin.

# Define the search and replace strings.
AS3CF_FILE=/var/www/html/public/app/mu-plugins/amazon-s3-and-cloudfront-pro/classes/upgrades/upgrade.php
AS3CF_SEARCH="\$this->items_processed++;"
AS3CF_NEW="if(\$upgraded % 100 === 0) { error_log(\"AS3CF_Upgrade - total \$total, items_processed \$this->items_processed, upgrade_name \$this->upgrade_name\"); }"
AS3CF_REPLACE="$AS3CF_SEARCH\n\n\t\t\t$AS3CF_NEW"

# If search string is in file. Then replace it.
if grep -q  $AS3CF_SEARCH $AS3CF_FILE ; then
  echo "Adding logging to amazon-s3-and-cloudfront-pro..."
  sed -i "s/$AS3CF_SEARCH/$AS3CF_REPLACE/g" $AS3CF_FILE
fi

TOTAL_POLL_FILE=/var/www/html/public/app/plugins/totalpoll-lite/src/Plugin.php
TOTAL_POLL_SEARCH="\${tooltip}"
TOTAL_POLL_REPLACE="{\$tooltip}"

# If search string is in file. Then replace it.
if grep -q  $TOTAL_POLL_SEARCH $TOTAL_POLL_FILE ; then
  echo "Fixing syntax error in totalpoll-lite..."
  sed -i "s/$TOTAL_POLL_SEARCH/$TOTAL_POLL_REPLACE/g" $TOTAL_POLL_FILE
fi
