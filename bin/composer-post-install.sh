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


TOTAL_POLL_FILE=/var/www/html/public/app/plugins/totalpoll-lite/src/Plugin.php
TOTAL_POLL_SEARCH="\${tooltip}"
TOTAL_POLL_REPLACE="{\$tooltip}"

# If search string is in file. Then replace it.
if grep -q  $TOTAL_POLL_SEARCH $TOTAL_POLL_FILE ; then
  echo "Fixing syntax error in totalpoll-lite..."
  sed -i "s/$TOTAL_POLL_SEARCH/$TOTAL_POLL_REPLACE/g" $TOTAL_POLL_FILE
fi


MOJ_COMPONENTS_FILE=/var/www/html/public/app/mu-plugins/wp-moj-components/component/Introduce/Introduce.php
MOJ_COMPONENTS_SEARCH_EMAIL="justice\.web@digital\.justice\.gov\.uk"
MOJ_COMPONENTS_REPLACE_EMAIL="intranet-support@digital.justice.gov.uk"

# If search string is in file. Then replace it.
if grep -q  $MOJ_COMPONENTS_SEARCH_EMAIL $MOJ_COMPONENTS_FILE ; then
  echo "Replacing email text in wp-moj-components plugin."
  sed -i "s/$MOJ_COMPONENTS_SEARCH_EMAIL/$MOJ_COMPONENTS_REPLACE_EMAIL/g" $MOJ_COMPONENTS_FILE
fi

MOJ_COMPONENTS_SEARCH_PARAGRAPH="<p>This website is ([\w\W]*?)<\/p>"
MOJ_COMPONENTS_REPLACE_PARAGRAPH="<p>This website is technically maintained by Justice Digital, Central Digital Product Team:<\/p>"

if [ -f "$MOJ_COMPONENTS_FILE" ] ; then
  echo "Replacing paragraph text in wp-moj-components plugin"
  MOJ_COMPONENTS_CONTENT=$(perl -0777pe 's/'"$MOJ_COMPONENTS_SEARCH_PARAGRAPH"'/'"$MOJ_COMPONENTS_REPLACE_PARAGRAPH"'/s' "$MOJ_COMPONENTS_FILE")
  echo "$MOJ_COMPONENTS_CONTENT" > "$MOJ_COMPONENTS_FILE"
fi


NOTIFY_FILE=/var/www/html/public/app/plugins/notify-for-wordpress/inc/admin/class-dashboard-table.php
NOTIFY_SEARCH="public function get_columns"
NOTIFY_REPLACE='private \$plugin_text_domain;

	public function __construct(string \$plugin_text_domain)
	{ 
		parent::__construct();
		\$this->plugin_text_domain = \$plugin_text_domain;
	}

	public function get_columns'

if [ -f "$NOTIFY_FILE" ] ; then
  echo "Adding code block to notify-for-wordpress plugin"
  NOTIFY_CONTENT=$(perl -0777pe 's/'"$NOTIFY_SEARCH"'/'"$NOTIFY_REPLACE"'/s' "$NOTIFY_FILE")
  echo "$NOTIFY_CONTENT" > "$NOTIFY_FILE"
fi


TREE_VIEW_FILE=/var/www/html/public/app/plugins/cms-tree-page-view/functions.php
TREE_VIEW_SEARCH="htmlspecialchars_decode(\$editLink)"
TREE_VIEW_REPLACE="htmlspecialchars_decode(\$editLink ?? '')"

# If search string is in file. Then replace it.
if grep -q  $TREE_VIEW_SEARCH $TREE_VIEW_FILE ; then
  echo "Fixing warning in cms-tree-page-view..."
  sed -i "s/$TREE_VIEW_SEARCH/$TREE_VIEW_REPLACE/g" $TREE_VIEW_FILE
fi

# Check that the version of wp-document-revisions is ont that's been confirmed to work.
verify_composer_package_version "wpackagist-plugin/wp-document-revisions" "3.6.1"

DOCUMENT_REVISIONS_FILE=/var/www/html/public/app/mu-plugins/wp-document-revisions/includes/class-wp-document-revisions-admin.php

DOCUMENT_REVISIONS_SEARCH_1="\$revisions    = \$this->get_revisions( \$post->ID );"
DOCUMENT_REVISIONS_REPLACE_1="\$revisions    = apply_filters('wp_document_revisions_get_revisions', \$this->get_revisions( \$post->ID ), 'revision_metabox');"

DOCUMENT_REVISIONS_SEARCH_2="\$latest_version = \$this->get_latest_revision( \$post->ID );"
DOCUMENT_REVISIONS_REPLACE_2="\$latest_version = apply_filters('wp_document_revisions_get_latest_revision', \$this->get_latest_revision( \$post->ID ), 'document_metabox');"

# If the file exists, then replace the search strings.
if [ -f "$DOCUMENT_REVISIONS_FILE" ] ; then
  echo "Adding wp_document_revisions_get_revisions filter to wp-document-revisions..."
  sed -i "s/$DOCUMENT_REVISIONS_SEARCH_1/$DOCUMENT_REVISIONS_REPLACE_1/g" $DOCUMENT_REVISIONS_FILE

  echo "Adding wp_document_revisions_get_latest_revision filter to wp-document-revisions..."
  sed -i "s/$DOCUMENT_REVISIONS_SEARCH_2/$DOCUMENT_REVISIONS_REPLACE_2/g" $DOCUMENT_REVISIONS_FILE
fi
