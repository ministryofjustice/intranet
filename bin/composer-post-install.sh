#!/usr/bin/env sh

# A function to get the installed version of a composer package.
get_installed_version() {
  composer show $1 | sed -n '/versions/s/^[^0-9]\+\([^,]\+\).*$/\1/p'
}

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
  echo "Adding code blocke to notify-for-wordpress plugin"
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


# Plugin version check.
ELASTIC_PRESS_TARGET_PACKAGE="wpackagist-plugin/elasticpress"
ELASTIC_PRESS_TARGET_VERSION="5.1.3"
ELASTIC_PRESS_INSTALLED_VERSION=$(get_installed_version $ELASTIC_PRESS_TARGET_PACKAGE)

# If the target version is not inatalled then exit.
if [ "$ELASTIC_PRESS_INSTALLED_VERSION" != "$ELASTIC_PRESS_TARGET_VERSION" ] ; then
  echo "Elasticpress target version is not installed - review composer-post-install.sh."
  exit 1;
fi

# Variables for the find and replace.
ELASTIC_PRESS_FILE=/var/www/html/public/app/mu-plugins/elasticpress/includes/classes/Indexable/Post/SyncManager.php
ELASTIC_PRESS_SEARCH="\t\$this->action_delete_post( \$post_id );"
ELASTIC_PRESS_REPLACE="\t\$indexable->get( \$post_id ) \&\& \$this->action_delete_post( \$post_id );"

if [ -f "$ELASTIC_PRESS_FILE" ] ; then
  echo "Fixing warning in elasticpress. Checking for doc before deleting prevents 404s in logs..."
  sed -i "s/$ELASTIC_PRESS_SEARCH/$ELASTIC_PRESS_REPLACE/g" $ELASTIC_PRESS_FILE
fi
