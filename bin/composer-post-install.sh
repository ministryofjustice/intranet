#!/usr/bin/env sh

# A function to verify version is supported, exit it it's not.
verify_composer_package_version() {
  TARGET_PACKAGE=$1
  TARGET_VERSION=$2
  INSTALLED_VERSION=$(composer show $1 | sed -n '/versions/s/^[^0-9]\+\([^,]\+\).*$/\1/p')

  if [ "$INSTALLED_VERSION" != "$TARGET_VERSION" ] ; then
    echo "$TARGET_PACKAGE target version is not installed - review composer-post-install.sh."
    exit 1;
  fi
}

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"

MOJ_COMPONENTS_FILE=$ROOT_DIR/public/app/mu-plugins/wp-moj-components/component/Introduce/Introduce.php
MOJ_COMPONENTS_SEARCH_EMAIL="justice\.web@digital\.justice\.gov\.uk"
MOJ_COMPONENTS_REPLACE_EMAIL="intranet-support@digital.justice.gov.uk"

# If search string is in file. Then replace it.
if grep -q  $MOJ_COMPONENTS_SEARCH_EMAIL $MOJ_COMPONENTS_FILE ; then
  echo "Replacing email text in wp-moj-components plugin."
  sed -i "s/$MOJ_COMPONENTS_SEARCH_EMAIL/$MOJ_COMPONENTS_REPLACE_EMAIL/g" $MOJ_COMPONENTS_FILE
fi

MOJ_COMPONENTS_SEARCH_PARAGRAPH_1="MoJ Digital \& Technology"
MOJ_COMPONENTS_REPLACE_PARAGRAPH_1="Justice Digital"
MOJ_COMPONENTS_SEARCH_PARAGRAPH_2="Justice on the Web team"
MOJ_COMPONENTS_REPLACE_PARAGRAPH_2="Central Digital Product Team"

if grep -q "$MOJ_COMPONENTS_SEARCH_PARAGRAPH_1" "$MOJ_COMPONENTS_FILE" ; then
  echo "Replacing paragraph text 1 in wp-moj-components plugin"
  sed -i "s/$MOJ_COMPONENTS_SEARCH_PARAGRAPH_1/$MOJ_COMPONENTS_REPLACE_PARAGRAPH_1/g" "$MOJ_COMPONENTS_FILE"
fi
if grep -q "$MOJ_COMPONENTS_SEARCH_PARAGRAPH_2" "$MOJ_COMPONENTS_FILE" ; then
  echo "Replacing paragraph text 2 in wp-moj-components plugin"
  sed -i "s/$MOJ_COMPONENTS_SEARCH_PARAGRAPH_2/$MOJ_COMPONENTS_REPLACE_PARAGRAPH_2/g" "$MOJ_COMPONENTS_FILE"
fi
