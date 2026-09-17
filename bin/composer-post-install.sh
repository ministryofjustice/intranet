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


MOJ_COMPONENTS_FILE=/var/www/html/public/app/mu-plugins/wp-moj-components/component/Introduce/Introduce.php
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


# Check that the version of wp-document-revisions is one that's been confirmed to work.
verify_composer_package_version "wpackagist-plugin/wp-document-revisions" "5.4.2"

# Since v4, the document and revision metaboxes live in the admin-editor trait, and call methods on `$wpdr`.
DOCUMENT_REVISIONS_FILE=/var/www/html/public/app/mu-plugins/wp-document-revisions/includes/trait-wp-document-revisions-admin-editor.php

DOCUMENT_REVISIONS_SEARCH_1="\$revisions    = \$wpdr->get_revisions( \$post->ID );"
DOCUMENT_REVISIONS_REPLACE_1="\$revisions    = apply_filters('wp_document_revisions_get_revisions', \$wpdr->get_revisions( \$post->ID ), 'revision_metabox');"
DOCUMENT_REVISIONS_PATCHED_1="apply_filters('wp_document_revisions_get_revisions'"

DOCUMENT_REVISIONS_SEARCH_2="\$latest_version = \$wpdr->get_latest_revision( \$post->ID );"
DOCUMENT_REVISIONS_REPLACE_2="\$latest_version = apply_filters('wp_document_revisions_get_latest_revision', \$wpdr->get_latest_revision( \$post->ID ), 'document_metabox');"
DOCUMENT_REVISIONS_PATCHED_2="apply_filters('wp_document_revisions_get_latest_revision'"

# The file must exist - the theme depends on these filters to show the correct revision author.
if [ ! -f "$DOCUMENT_REVISIONS_FILE" ] ; then
  echo "wp-document-revisions file not found: $DOCUMENT_REVISIONS_FILE - review composer-post-install.sh."
  exit 1;
fi

echo "Adding wp_document_revisions_get_revisions filter to wp-document-revisions..."
sed -i "s/$DOCUMENT_REVISIONS_SEARCH_1/$DOCUMENT_REVISIONS_REPLACE_1/g" $DOCUMENT_REVISIONS_FILE

echo "Adding wp_document_revisions_get_latest_revision filter to wp-document-revisions..."
sed -i "s/$DOCUMENT_REVISIONS_SEARCH_2/$DOCUMENT_REVISIONS_REPLACE_2/g" $DOCUMENT_REVISIONS_FILE

# sed does not fail when there is no match, so verify that both filters are now present.
if ! grep -qF "$DOCUMENT_REVISIONS_PATCHED_1" "$DOCUMENT_REVISIONS_FILE" || ! grep -qF "$DOCUMENT_REVISIONS_PATCHED_2" "$DOCUMENT_REVISIONS_FILE" ; then
  echo "Failed to add filters to wp-document-revisions - review composer-post-install.sh."
  exit 1;
fi
