#!/usr/bin/env bash

#
# make executable and move to bin
#
if [[ -f /opt/mongo-migrations/bin/mm ]]; then
  unlink /opt/mongo-migrations/bin/mm
fi

if [[ -f /opt/mongo-migrations/bin/mm.phar ]]; then
	chmod +x /opt/mongo-migrations/bin/mm.phar
	mv /opt/mongo-migrations/bin/mm.phar /opt/mongo-migrations/bin/mm
fi
