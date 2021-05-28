#!/usr/bin/env bash
#

read -s -p "password: " pword
echo ""

DIR="$( cd "$( dirname "$0" )" && pwd )"

NOW=$(date +"%Y%m%dT%H%M%S")

mongodump --authenticationDatabase=FpdbDk -u vagrant -p $pword -o "$DIR/../dev/mongo/dump"

cd "$DIR/../dev/mongo"
tar vczf "mongodata-$NOW.tgz" dump

rm -r "$DIR/../dev/mongo/dump"