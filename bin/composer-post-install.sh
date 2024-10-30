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


MOJ_COMPONENTS_FILE=/var/www/html/public/app/mu-plugins/wp-moj-components/component/Introduce/Introduce.php
MOJ_COMPONENTS_SEARCH_1="justice\.web@digital\.justice\.gov\.uk"
MOJ_COMPONENTS_REPLACE_1="intranet-support@digital.justice.gov.uk"
MOJ_COMPONENTS_SEARCH_2="Justice\son\sthe\sWeb\steam"
MOJ_COMPONENTS_REPLACE_2="Central Digital Product Team"

# If search string is in file. Then replace it.
if grep -q  $MOJ_COMPONENTS_SEARCH_1 $MOJ_COMPONENTS_FILE ; then
  echo "Replacing email text in wp-moj-components plugin."
  sed -i "s/$MOJ_COMPONENTS_SEARCH_1/$MOJ_COMPONENTS_REPLACE_1/g" $MOJ_COMPONENTS_FILE
fi

if grep -q  $MOJ_COMPONENTS_SEARCH_2 $MOJ_COMPONENTS_FILE ; then
  echo "Replacing team text in wp-moj-components plugin."
  sed -i "s/'$MOJ_COMPONENTS_SEARCH_2'/'$MOJ_COMPONENTS_REPLACE_2'/g" $MOJ_COMPONENTS_FILE
fi
