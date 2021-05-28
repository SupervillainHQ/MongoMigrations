#!/usr/bin/env bash
#

read -s -p "password: " pword
echo ""

DIR="$( cd "$( dirname "$0" )" && pwd )"
FILE=$1
outDir="$DIR/../dev/mongo"

echo $outDir
exit


tar -xvf $FILE -C $outDir

mongorestore --authenticationDatabase=FpdbDk -u vagrant -p $pword "$DIR/../dev/mongo/dump"

rm -r "$DIR/../dev/mongo/dump"