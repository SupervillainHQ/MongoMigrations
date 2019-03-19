#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "$0" )" && pwd )"
CURRENT=`pwd`
PATH=${1:-"/var/tmp/to-process"}
HOST=${1:-"dev.customers.tasklift.com"}

# Ensure that the logrotate.conf file is using local paths and is installed in the logrotate conf.d folder
sed -e "s;%PATH%;$PATH;g" "$CURRENT/resources/logrotate.conf" > "$PATH/logrotate.conf"
ln -fs "$CURRENT/logrotate.conf" "/etc/logrotate.d/$HOST"


mkdir -p /var/www/customers.tasklift.com
chown jenkins:www-data /var/www/customers.tasklift.com
chmod ug+s /var/www/customers.tasklift.com


echo "{\"serverName\" : \"$HOST\"}" | mustache - "$deployDirRelease/resources/apache.conf" > /var/www/customers.tasklift.com/customers.tasklift.com.conf
chmod go-w /var/www/customers.tasklift.com/customers.tasklift.com.conf
