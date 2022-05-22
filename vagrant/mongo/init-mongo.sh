#!/usr/bin/env bash
#
#

DIR="$( cd "$( dirname "$0" )" && pwd )"

# Create root user and daily user
# (mongo prompt understands javascript)
mongo "$DIR/create-users.js"

# requires sudo or root
if [ "$EUID" -ne 0 ]
  then echo "Root privileges missing. Please enable authorization and restart mongod service manually"
  exit
fi

# Set up authentication
sed -i.bak "s/^#security:/security:\\n  authorization: enabled/" /etc/mongod.conf

service mongod restart