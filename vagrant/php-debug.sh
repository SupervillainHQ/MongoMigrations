#!/usr/bin/env bash

export XDEBUG_CONFIG="idekey=PHPSTORM"
export PHP_IDE_CONFIG="serverName=Xdebug on Vagrant"
php "$@"