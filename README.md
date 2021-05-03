# MongoMigrations

## Composer install

    composer install supervillainhq/mongomigrations

# Required set up

## Config files (mm.json and an auth.json)

Create an 'mm.json' json file in a directory that the MongoMigrations binary (vendor/bin/mm) can find: ./config/

Add the following contents:

    {
      "database" : "<database-name>",
      "host" : "127.0.0.1",
      "port" : "27017",
      "authorization" : {
        "file" : "<path-to-a-json-file-with-auth-info>"
      },
      "migrations" : {
        "path" : "<path-to-migrations-directory>",
        "entries" : "mm_migration_log"
      }
    }

The authorization.file auth-file should have the following format:

    {
      "user": "<username>",
      "password": "<password>",
      "authenticationDatabase": "<auth-database>"
    }


# Commands

### Options

#### Command (-c)

Required option.

#### Verbose (-v)

Optional. Displays debug information

## Help

Display some help

    php vendor/bin/mm -c Help


## Init

Create the migration directory where Collection migration files.

    php vendor/bin/mm -c Create <collection>

## Config

A configuration tool for managing configurations

    php vendor/bin/mm -c Config <sub-command

## Create

Create a Collection migration file that can be added to git in order to port Collections to remote environments.

    php vendor/bin/mm -c Create <collection>


## Migrate

Evaluates the list of Collection migrations and determines which collections are missing. Then creates the missing Collections.

    php vendor/bin/mm -c Migrate


Created Collections are logged locally on any (remote) environment in which the command is executed. The log is itself a mongo collection (mm_migration_log).
