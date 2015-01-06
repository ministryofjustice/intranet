#!/bin/sh

# NOTE: Create a new file deploy_dev.cfg in the same directory and set these two
# variables in that file (they are for your local database settings):
# db_user=''
# db_pass=''
#
# deploy_dev.sh should be under version control
# deploy_dev.cfg should NOT be under version control

source deploy_dev.cfg

# deploy all files
rsync -r --delete --progress --exclude=deploy_dev.sh --exclude=wp-config.php -e ssh ./* 54aa7710e0b8cdcf560000e5@php-mojintranet.rhcloud.com:/var/lib/openshift/54aa7710e0b8cdcf560000e5/app-root/runtime/repo/

# create and deploy mysql dump
mysqldump -u $db_user --password=$db_pass mojintranet >> deploy_dump.sql
rsync --progress -e ssh ./deploy_dump.sql 54aa7710e0b8cdcf560000e5@php-mojintranet.rhcloud.com:/var/lib/openshift/54aa7710e0b8cdcf560000e5/app-root/runtime/

# import the mysql dump on the server
ssh 54aa7710e0b8cdcf560000e5@php-mojintranet.rhcloud.com <<ENDSSH
mysql php < /var/lib/openshift/54aa7710e0b8cdcf560000e5/app-root/runtime/deploy_dump.sql
ENDSSH

# delete the local dump file (we still keep the remote one - means faster deploys as the file doesn't always need to be transferred)
rm deploy_dump.sql
