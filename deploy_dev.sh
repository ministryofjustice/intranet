#!/bin/sh

# NOTE: Create a new file deploy_dev.cfg in the same directory and set these two
# variables in that file (they are for your local database settings):
# db_user=''
# db_pass=''
#
# deploy_dev.sh should be under version control
# deploy_dev.cfg should NOT be under version control

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

source deploy_dev.cfg

# deploy all files
rsync -r --delete --progress --exclude=deploy_dev.sh --exclude=wp-config.php -e ssh ./* $user@$app-mojintranet.rhcloud.com:/var/lib/openshift/$user/app-root/runtime/repo/

# create and deploy mysql dump
mysqldump -u $db_user --password=$db_pass mojintranet >> deploy_dump.sql
rsync --progress -e ssh ./deploy_dump.sql $user@$app-mojintranet.rhcloud.com:/var/lib/openshift/$user/app-root/runtime/

# import the mysql dump on the server
ssh $user@$app-mojintranet.rhcloud.com <<ENDSSH
mysql $app < /var/lib/openshift/$user/app-root/runtime/deploy_dump.sql
ENDSSH

# delete the local dump file (we still keep the remote one - means faster deploys as the file doesn't always need to be transferred)
rm deploy_dump.sql
