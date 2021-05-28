#!/usr/bin/env bash
#
#

# Service restart requires root privileges
if [ "$EUID" -ne 0 ]
  then echo "Root privileges missing. Please run as root"
  exit
fi

DIR="$( cd "$( dirname "$0" )" && pwd )"
dbName=${1:-''}

if [[ -z "$1" ]]
then
  echo "Missing argument dbName"
  exit
fi


# Create root user and daily user
# (mongo prompt understands javascript)
mongo --eval "let dbName = \"$dbName\";" "$DIR/create-users.js"

# Set up authentication
#
sed -i.bak "s/^#security:/security:\\n  authorization: enabled/" /etc/mongod.conf

service mongod restart