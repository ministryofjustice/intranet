#!/usr/bin/env bash
#author Damien Wilson

set -e

# pick a dev environment
if [[ "$WP_ENV" != "development" ]]; then
	echo "Only run this script in a local docker instance"
	exit 0
fi

# let's get started...
# reset
wp --allow-root db reset

for i in *.sql; do
    wp --allow-root db import "$i"

    # only run the first .sql file
    break
done

echo "- - - - - - - - - - - - - - - - -"
read -p "Enter the URL of the site most used in the backup. This will be like http://thesite.gov.uk - don't include a forward slash: " IMPORTED_SERVER_NAME

echo "The URL entered was: $IMPORTED_SERVER_NAME"
echo "To be replaced with: $WP_HOME"

read -p "OK to continue? [y/n] " -e -i 'y' CAN_CONTINUE

if [[ "$CAN_CONTINUE" =~ "y" ]]; then
	wp --allow-root search-replace "$IMPORTED_SERVER_NAME" "$WP_HOME" --recurse-objects --skip-columns=guid --skip-tables=wp_users
else
	echo "The operation was aborted"
	exit 0
fi

# time to update with out using a URL protocol
NO_PROTOCOL_IMPORTED_NAME=${IMPORTED_SERVER_NAME/https\:\/\//""}

echo "- - - - - - - - - - - - - - - - -"
echo "NEW SEARCH..."
echo "URL we need to find: $NO_PROTOCOL_IMPORTED_NAME"
echo "To be replaced with: $VIRTUAL_HOST"

read -p "OK to continue? [y/n] " -e -i 'y' CAN_CONTINUE_2

if [[ "$CAN_CONTINUE_2" =~ "y" ]]; then
    wp --allow-root search-replace "$NO_PROTOCOL_IMPORTED_NAME" "$VIRTUAL_HOST" --recurse-objects --skip-columns=guid --skip-tables=wp_users
else
    echo "The DB has been modified. Please run this script again to reset. The operation was aborted"
    exit 0
fi


# time to add a user, if required
echo "- - - - - - - - - - - - - - - - -"
read -p "Create a user account? [y/n] " -e -i 'y' IS_CREATE_USER_ACCOUNT

if [[ "$IS_CREATE_USER_ACCOUNT" =~ "y" ]]; then
    read -p "Enter your digital.justice email address: " MOJ_WP_EMAIL
    echo "Creating user..."

    MOJ_WP_USERNAME=$(echo "${MOJ_WP_EMAIL%%@*}" | sed 's/[.-]//g')
    MOJ_WP_PASSWORD=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 12 | head -n 1)

    wp --allow-root user create "$MOJ_WP_USERNAME" "$MOJ_WP_EMAIL" --role=administrator --user_pass="$MOJ_WP_PASSWORD"

    echo ""
    echo "Thank you. Please take note of your new username and password: "
    echo "- - - - - - - - - - - - -"
    echo "Username: $MOJ_WP_USERNAME"
    echo "Password: $MOJ_WP_PASSWORD"
    echo "- - - - - - - - - - - - -"


    echo "Great! Login here: $WP_HOME/wp-admin/"
else
    echo "The 'create user' operation was skipped. The database has been modified."
    echo "Visit the site here: $WP_HOME/"
fi

exit 0
