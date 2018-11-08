#!/bin/sh

# Update version numbers throughout the app.
# First time you run this app make sure to make this file executable by running 'chmod +x ./version.sh' on this file in commmand line.

RESTORE='\033[0m'
YELLOW='\033[00;33m'
GREEN='\033[00;32m'

function msg {
  echo "$1"$RESTORE
}

cat <<'EOF'
                    _                __  _                
 _   _____  __________(_)___  ____     / /_(_)___ ____  _____
| | / / _ \/ ___/ ___/ / __ \/ __ \   / __/ / __ `/ _ \/ ___/
| |/ /  __/ /  (__  ) / /_/ / / / /  / /_/ / /_/ /  __/ /    
|___/\___/_/  /____/_/\____/_/ /_/   \__/_/\__, /\___/_/     
                                          /____/

VERSION TIGER
Updating version numbers in Clarity theme..

This script updates three files: 
package.json, 
inc/enqueue.php, 
style.css

EOF

if [ -e package.json ]
then
  previous_version=$(node -pe "require('./package.json').version")
  msg $GREEN"Current site version is: ${previous_version}"
else
  msg $YELLOW"Cannot find package.json in Clarity root. Exiting."
  exit 0
fi

cat <<'EOF'
major.minor.patch
EOF

printf "Enter new version number: "
read -r new_version

regex="^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$"

if [[ $new_version =~ $regex ]]
then
msg $GREEN"Updating style.css"
sed -i "" "s/${previous_version}/${new_version}/g" style.css

msg $GREEN"Updating inc/enqueue.php"
sed -i "" "s/${previous_version}/${new_version}/g" inc/enqueue.php

msg $GREEN"Updating package.json"
sed -i "" "3s/${previous_version}/${new_version}/g" package.json

msg "You have successfully updated the version from $YELLO${previous_version} to $GREEN${new_version}"
else
  msg $YELLOW"Bite. VersionTiger is not happy with your sausage fingers. That is not a correct version number. Exiting."
fi
exit 0
