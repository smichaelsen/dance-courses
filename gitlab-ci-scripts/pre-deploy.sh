#!/bin/bash

scp nginx.conf $PRIVILEGED_SSH_USERNAME@$SSH_HOST:~ && ssh $PRIVILEGED_SSH_USERNAME@$SSH_HOST "sudo mv nginx.conf /usr/local/etc/nginx/vhosts/shl.conf && sudo service nginx reload"
rm nginx.conf

if [ -z "$DBPASS_ROOT" ]; then exit 0 ; fi

echo 'create database if not exists '$DBNAME'; grant all on `'$DBNAME'`.* to '$DBUSER'@localhost identified by "'$DBPASS'";' >> sql
scp sql $SSH_HOST@$SSH_HOST:~
ssh $SSH_HOST@$SSH_HOST "mysql --user='root' --password='$DBPASS_ROOT' --host='$DBHOST' < sql && rm sql"
rm sql
