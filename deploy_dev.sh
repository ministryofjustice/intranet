#!/bin/sh

# NOTE: The script will save your local user/pass for the DB in deploy_dev.cfg
#
# deploy_dev.sh should be under version control
# deploy_dev.cfg should NOT be under version control

function get_db_details() {
  printf "Local database username: "
  read -r db_user
  printf "Local database password: "
  read -rs db_pass
  echo
  printf "Local database name: "
  read -r db_db
  echo
}

case $1 in
  dev1)
    user="54b3d82be0b8cd1b9b000063"
    app="dev1"
    ;;
  dev2)
    user="54b3d6954382ec279f00001d"
    app="dev2"
    ;;
  editor)
    user="54aa7710e0b8cdcf560000e5"
    app="php"
    ;;
  *)
    echo "Environment not specified or incorrect. Aborting..." 1>&2
    exit 1
    ;;
esac

config_file='deploy_dev.cfg'

if [ ! -f $config_file ];
then
  get_db_details

  # try to connect to local DB
  while ! mysql -u $db_user --password=$db_pass $db_db -e ";" ;
  do
    echo
    echo "Try again... "
    get_db_details
  done

  echo "db_user='$db_user'" >> $config_file
  echo "db_pass='$db_pass'" >> $config_file
  echo "db_db='$db_db'" >> $config_file
fi

source $config_file

# deploy all files
rsync -rlptu --delete --progress --exclude='.htaccess' --exclude=deploy_dev.sh --exclude=deploy_dev.cfg --exclude=wp-config.php -e ssh ./ $user@$app-mojintranet.rhcloud.com:/var/lib/openshift/$user/app-root/runtime/repo/

# create and deploy mysql dump
mysqldump -u $db_user --password=$db_pass $db_db >> deploy_dump.sql
rsync -zu --progress -e ssh ./deploy_dump.sql $user@$app-mojintranet.rhcloud.com:/var/lib/openshift/$user/app-root/runtime/

# import the mysql dump on the server
ssh $user@$app-mojintranet.rhcloud.com <<ENDSSH
  mysql $app < /var/lib/openshift/$user/app-root/runtime/deploy_dump.sql
ENDSSH

# delete the local dump file (we still keep the remote one - means faster deploys as the file doesn't always need to be transferred)
rm deploy_dump.sql
